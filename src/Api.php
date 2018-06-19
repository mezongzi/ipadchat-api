<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/6/11
 * Time: 下午10:09
 */

namespace PadChat;

use GuzzleHttp\Client as HttpClient;

class Api
{
    /**
     * 默认请求URI
     * @var string
     */
    protected $base_uri = "https://wxapi.fastgoo.net/";

    /**
     * 请求超时时间
     * @var float|int
     */
    protected $timeout = 30.0;

    /**
     * HTTP类
     * @var HttpClient|null
     */
    protected $client = null;

    protected $config = [];

    /**
     * 初始化，如果本地化部署需要用户自己填写IP
     * Api constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        !empty($config['host']) && $this->base_uri = $config['host'];
        !empty($config['timeout']) && $this->timeout = $config['timeout'];
        $this->config = $config;
        $this->client = new HttpClient();
    }

    /**
     *
     * @param $wx_user
     */
    public function setWxHandle($wx_user)
    {
        $this->config['wx_user'] = $wx_user;
    }

    /**
     * 初始化微信实例，传入回调地址
     * @param $callback_url
     * @return mixed
     * @throws RequestException
     */
    public function init($callback_url)
    {
        return $this->post(__FUNCTION__, compact('callback_url'));
    }

    /**
     * 获取登录二维码
     * @return mixed
     * @throws RequestException
     */
    public function getLoginQrcode()
    {
        $res = $this->post(__FUNCTION__);
        !empty($res['data']['url']) && $res['data']['url'] = $this->base_uri . $res['data']['url'];
        return $res;
    }

    /**
     * 获取二维码的扫描状态
     * @return mixed
     * @throws RequestException
     */
    public function getQrcodeStatus()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 发送消息
     * 文本消息 $content 为字符串文字
     * 图片消息 $content['image'](base64编码) $content['image_size'](图片大小)
     * 语音消息 $content['voice'](base64编码) $content['voice_size'](语音大小)   silk格式
     * 分享名片 $content['contact_wxid']   $content['contact_name']
     * app消息 $content['app_msg'] xml格式字符串，主要用于发送链接
     * @param $user
     * @param $content
     * @param array $at_user
     * @return mixed
     * @throws RequestException
     */
    public function sendMsg($user, $content, $at_user = [])
    {
        if (!$user && !$content) {
            throw new RequestException('user或content不能为空', -1);
        }
        $req = [];
        if (is_array($content)) {
            $req = $content;
        } else {
            $req['content'] = $content;
        }
        $req['user'] = $user;
        $req['at_user'] = $at_user;
        return $this->post(__FUNCTION__, $req);
    }

    /**
     * 群发消息
     * @param $users
     * @param $content
     * @return mixed
     * @throws RequestException
     */
    public function massMsg($users, $content)
    {
        $users = json_encode($users);
        return $this->post(__FUNCTION__, compact('users', 'content'));
    }

    /**
     * 获取已登录的微信用户信息
     * @return mixed
     * @throws RequestException
     */
    public function getMyInfo()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 获取登录token
     * @return mixed
     * @throws RequestException
     */
    public function getLoginToken()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 获取wx_data数据
     * @return mixed
     * @throws RequestException
     */
    public function getWxData()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 获取消息图片
     * @param $image 消息字符串
     * @return mixed
     * @throws RequestException
     */
    public function getMsgImage($image)
    {
        return $this->post(__FUNCTION__, compact('image'));
    }

    /**
     * 获取消息视频
     * @param $video 消息字符串
     * @return mixed
     * @throws RequestException
     */
    public function getMsgVideo($video)
    {
        return $this->post(__FUNCTION__, compact('video'));
    }

    /**
     * 获取消息语音
     * @param $voice 消息字符串
     * @return mixed
     * @throws RequestException
     */
    public function getMsgVoice($voice)
    {
        return $this->post(__FUNCTION__, compact('voice'));
    }

    /**
     * 登录（token + wx_data） (username + password + wx_data) (phone + password + wx_data)
     * @param $params
     * @return mixed
     * @throws RequestException
     */
    public function login($params)
    {
        return $this->post(__FUNCTION__, $params);
    }

    /**
     * 退出登录实例
     * @return mixed
     * @throws RequestException
     */
    public function logout()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 关闭微信实例
     * @return mixed
     * @throws RequestException
     */
    public function close()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 同步通讯录
     * 获取continue字段为0则不需要再同步，需要过滤掉非通讯录数据
     * @return mixed
     * @throws RequestException
     */
    public function syncContact()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 通过wxid获取联系人信息 (联系人相关)
     * @param $wx_id
     * @return mixed
     * @throws RequestException
     */
    public function getContact($wx_id)
    {
        return $this->post(__FUNCTION__, ['user' => $wx_id]);
    }

    /**
     * 通过wxid搜索联系人信息，未加好友可以获取信息 (联系人相关)
     * @param $wx_id
     * @return mixed
     * @throws RequestException
     */
    public function searchContact($wx_id)
    {
        return $this->post(__FUNCTION__, ['user' => $wx_id]);
    }

    /**
     * 删除好友 (联系人相关)
     * @param $wx_id
     * @return mixed
     * @throws RequestException
     */
    public function deleteContact($wx_id)
    {
        return $this->post(__FUNCTION__, ['user' => $wx_id]);
    }

    /**
     * 获取二维码 (自己的、群聊) (联系人相关)
     * @param $wx_id
     * @return mixed
     * @throws RequestException
     */
    public function getContactQrcode($wx_id)
    {
        return $this->post(__FUNCTION__, ['user' => $wx_id]);
    }

    /**
     * 设置用户头像
     * @param $image
     * @param int $image_size
     * @return mixed
     * @throws RequestException
     */
    public function setHeadImage($image, $image_size = 0)
    {
        return $this->post(__FUNCTION__, compact('image', 'image_size'));
    }

    /**
     * 同意好友申请
     * @param $stranger
     * @param $ticket
     * @return mixed
     * @throws RequestException
     */
    public function acceptUser($stranger, $ticket)
    {
        return $this->post(__FUNCTION__, compact('stranger', 'ticket'));
    }

    /**
     * 添加好友
     * stranger v2 type desc
     * @param $params
     * @return mixed
     * @throws RequestException
     */
    public function addUser($params)
    {
        return $this->post(__FUNCTION__, $params);
    }

    /**
     * 退出群聊
     * @param $room
     * @return mixed
     * @throws RequestException
     */
    public function quitRoom($room)
    {
        return $this->post(__FUNCTION__, compact('room'));
    }

    /**
     * 删除群成员
     * @param $room
     * @param $user
     * @return mixed
     * @throws RequestException
     */
    public function deleteRoomMember($room, $user)
    {
        return $this->post(__FUNCTION__, compact('room', 'user'));
    }

    /**
     * 获取群成员列表
     * @param $room
     * @return mixed
     * @throws RequestException
     */
    public function getRoomMembers($room)
    {
        return $this->post(__FUNCTION__, compact('room'));
    }

    /**
     * 创建群聊
     * @param $members
     * @return mixed
     * @throws RequestException
     */
    public function createRoom($members)
    {
        $members = json_encode($members);
        return $this->post(__FUNCTION__, compact('members'));
    }

    /**
     * 设置群公告
     * @param $room
     * @param $content
     * @return mixed
     * @throws RequestException
     */
    public function setRoomAnnouncement($room, $content)
    {
        return $this->post(__FUNCTION__, compact('room', 'content'));
    }

    /**
     * 设置群名称
     * @param $room
     * @param $name
     * @return mixed
     * @throws RequestException
     */
    public function setRoomName($room, $name)
    {
        return $this->post(__FUNCTION__, compact('room', 'name'));
    }

    /**
     * 邀请好友入群
     * @param $room
     * @param $user
     * @param int $type 1直接邀请 2发送链接
     * @return mixed
     * @throws RequestException
     */
    public function addRoomMember($room, $user, $type = 2)
    {
        return $this->post(__FUNCTION__, compact('room', 'user', 'type'));
    }


    /**
     * post 请求
     * @param $url
     * @param $params
     * @return mixed
     * @throws RequestException
     */
    private function post($url, $params = [])
    {
        if (!is_array($params)) {
            throw new RequestException("请求参数必须为数组", -1);
        }
        !empty($this->config['wx_user']) && $params['wx_user'] = $this->config['wx_user'];
        $params['timestamp'] = time();
        $params['sign'] = Util::makeSign($params, !empty($this->config['secret']) ? $this->config['secret'] : '123');
        $response = $this->client->request('POST', $this->base_uri . $url, [
            'form_params' => $params
        ]);

        if ($response->getStatusCode() != 200) {
            throw new RequestException("请求接口失败了，响应状态码：" . $response->getStatusCode(), $response->getStatusCode());
        }
        $ret = $response->getBody()->getContents();
        if (!$ret) {
            throw new RequestException("未收到任何响应信息", -1);
        }
        $resStr = json_decode($ret, true);
        if (!$resStr) {
            throw new RequestException("接口返回的数据非JSON格式：" . $response->getBody()->getContents(), -1);
        }
        return $resStr;
    }
}
// index.js
// 获取应用实例
const app = getApp();
const defaultAvatarUrl = 'https://mmbiz.qpic.cn/mmbiz/icTdbqWNOwNRna42FI242Lcia07jQodd2FJGIYQfG0LAJGFxM4FbnQP6yfMxBgJ0F3YRqJCJ1aPAK2dQagdusBZg/0';

Page({
    data: {
        avatarUrl: defaultAvatarUrl,
        nickname: '点击确定您的昵称'
    },
    onChooseAvatar(e) {
        console.log(e);
        let that = this;
        const { avatarUrl } = e.detail;
        this.setData({
            avatarUrl,
        });
        wx.uploadFile({
            filePath: avatarUrl,
            name: 'file',
            url: '/upload/file',//服务器端接收图片的路径,注意：wx.uploadFile所允许的域名白名单需要在小程序后台配置，与wx.request是分开的。配置之后记得清除本地缓存才会生效。
            timeout:5000,
            success: function (res) {
                console.log(res);
                that.setData({
                    'avatarUrl': res
                });
            },
            fail: function (res) {
                console.log(res);//发送失败回调，可以在这里了解失败原因
            }
        })
    },

    /**
     * 输入框实时回调
     * @param {*} options
     */
    userNameInputAction(options) {
        let that = this;
        let value = options.detail.value;//获取输入框输入的内容
        console.log("输入框输入的内容是 " + value);
        that.setData({
            "nickname": value
        });
        // console.log("输出内容是 " + that.data.nickname);
    },

    submit() {
        var that = this;

        if (!that.data.nickname || that.data.nickname == '点击确定您的昵称') {
            wx.showToast({
                title: '用户昵称必填',
                icon: 'error',
                duration: 2000
            })
            return false;
        }
        if (!that.data.avatarUrl || that.data.avatarUrl == defaultAvatarUrl) {
            wx.showToast({
                title: '用户头像必填',
                icon: 'error',
                duration: 2000
            })
            return false;
        }

        app.apiRequest({
            url: '/user/update',
            method: 'post',
            data: {
                'nickname': that.data.nickname,
                'avatarUrl': that.data.avatarUrl,
            },
            success: res => {
                wx.redirectTo({
                    url: wx.getStorageSync('activity_url')
                });
            }
        })

    },

    onLoad() {
        wx.hideHomeButton({
            success: function () {
                console.log("隐藏成功！")
            }
        })
    }
})

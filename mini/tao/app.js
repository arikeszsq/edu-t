// app.js
App({
    onLaunch() {
        // // 展示本地存储能力
        // const logs = wx.getStorageSync('logs') || []
        // logs.unshift(Date.now())
        // wx.setStorageSync('logs', logs)

        // // 登录
        // wx.login({
        //   success: res => {
        //     // 发送 res.code 到后台换取 openId, sessionKey, unionId
        //   }
        // })
        this.getUserInfo();

    },

    /**
     * 登陆并获取用户信息、token
     * @param {*} callback
     */
    getUserInfo: function (callback = null) {
        // 登录
        wx.login({
            success: res => {
                // 发送 res.code 到后台换取 openId, sessionKey, unionId
                var code = res.code;
                // 获取用户信息
                wx.getSetting({
                    success: res => {
                        if (res.authSetting['scope.userInfo']) {
                            // 已经授权，可以直接调用 getUserInfo 获取头像昵称，不会弹框
                            wx.getUserInfo({
                                success: res => {
                                    // 可以将 res 发送给后台解码出 unionId
                                    this.globalData.userInfo = res.userInfo
                                    this.globalData.hasUserInfo = true
                                    if (!this.checkIsLogin()) {
                                        console.log('no login');
                                        this.getToken(code, res.encryptedData, res.iv);
                                    }

                                    // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
                                    // 所以此处加入 callback 以防止这种情况
                                    callback && callback(res);
                                }
                            })
                        }
                    }
                })
            }
        });
    },

    globalData: {
        userInfo: null,
        hasUserInfo: false,
        apiDomain: 'http://edu.com.me/api',
        defaultDailyFreeParseNum: 10
    },


    //全局统一调用接口的方法
    apiRequest: function (options) {
        wx.request({
            url: this.globalData.apiDomain + options.url,
            method: options.method ? options.method : 'GET',
            header: {
                'Authorization': 'Bearer ' + wx.getStorageSync('token'),
                'Accept': 'application/json',
            },
            dataType: 'json',
            data: options.data,
            success: res => {
                switch (res.statusCode) {
                    case 200:
                        options.success(res);
                        break;
                    case 401:
                        this.toLogin();
                        break;
                    case 422:
                        break;
                    case 404:
                        wx.showToast({
                            title: '请求地址不存在',
                            icon: 'none'
                        })
                        break;
                    default:
                        wx.showToast({
                            title: '出错了～请稍后再试',
                            icon: 'none'
                        })
                }
            },
            fail: res => {
                if (options.fail) {
                    options.fail(res);
                }
            },
            complete: res => {
                if (options.complete) {
                    options.complete(res);
                }
            }
        });
    },

    /**
     * 验证登录
     */
    checkIsLogin(autoLogin = false) {
        if (wx.getStorageSync('token') != '') {
            return true;
        }
        if (autoLogin) {
            this.toLogin();
        } else {
            return false;
        }
    },

    /**
     * 跳转登陆页
     */
    toLogin() {
        this.globalData.hasUserInfo = false;
        this.globalData.userInfo = null;
        wx.removeStorageSync('token');
        wx.showToast({
            title: '请先登陆!',
            icon: 'none',
            success: res => {
                wx.switchTab({
                    url: '/pages/mine/mine'
                })
            }
        })
    },

    /**
     * 获取token
     */
    getToken(code, encryptedData, iv, callback = null) {
        //调后端接口获取token
        this.apiRequest({
            url: '/login',
            method: 'POST',
            data: {
                'code': code,
                'data': encryptedData,
                'iv': iv
            },
            success: res => {
                console.log(res.data.response.token);
                wx.setStorageSync('token', res.data.response.token);
                callback && callback();
            }
        });
    },

    toOneGroupList() {
        wx.setStorageSync('activity_type', 1)//活动类型 :1单商家，2多商家
        wx.navigateTo({
            url: "/pages/activity/group/index",
        })
    },
    toManyGroupList() {
        wx.setStorageSync('activity_type', 2);//活动类型 :1单商家，2多商家
        wx.setStorageSync('buy_method', 2);//购买方式 1 表示直接购买，购买方式 2，参团购买，这里是开团，参团的话，直接跳转到团列表页选择团
        wx.navigateTo({
            url: "/pages/activity/group/index",
        })
    },
    toCursePage() {
        wx.navigateTo({
            url: '/pages/course/course',
        })
    },
    //  音乐播放函数
    backmusic: function (music_url) {
        if(music_url){
            player(wx.getBackgroundAudioManager());
            function player(back) {
                back.title = "背景音乐";   // 必须要有一个title
                back.src = music_url;
                back.onEnded(() => {
                    player(wx.getBackgroundAudioManager());  // 音乐循环播放
                })
            }
        }
    },

    // 增加浏览人数
    addViewNum() {
        this.apiRequest({
            url: '/activity/view',
            method: 'get',
            data: {
                'activity_id': wx.getStorageSync('activity_id'),
                'share_user_id':wx.getStorageSync('share_user_id')
            },
            success: res => {
            }
        });
    },

    setMiniProgramShareCache() {
        let activity_id = wx.getStorageSync('activity_id') ? wx.getStorageSync('activity_id') : 2;
        this.apiRequest({
            url: '/user/share-info',
            method: 'post',
            data: {
                'activity_id': activity_id
            },
            success: res => {
                var user_id = res.data.response.user_id;
                let share_url = 'pages/index/index?activity_id=' + activity_id + '&invite_user_id' + user_id;
                let share_title = res.data.response.title;
                let share_imageUrl = res.data.response.bg ? res.data.response.bg : '';
                wx.setStorageSync('share_url', share_url);
                wx.setStorageSync('share_title', share_title);
                wx.setStorageSync('share_imageUrl', share_imageUrl);
            }
        });
    },

    newShareObj: function (options) {
        this.addShareNum();
        var shareObj = {
            title: wx.getStorageSync('share_title'),    // 默认是小程序的名称(可以写slogan等)
            path: wx.getStorageSync('share_url'),    // 默认是当前页面，必须是以‘/'开头的完整路径
            imageUrl: wx.getStorageSync('share_imageUrl'),//自定义图片路径，可以是本地文件路径、代码包文件路径或者网络图片路径，支持PNG及JPG，不传入 imageUrl 则使用默认截图。显示图片长宽比是 5:4
        }
        return shareObj;
    },

    // 增加分享次数
    addShareNum() {
        this.apiRequest({
            url: '/activity/add-share-num',
            method: 'post',
            data: {
                'activity_id': wx.getStorageSync('activity_id')
            },
            success: res => {
            }
        });
    },

    //添加订单成功，回调起支付
    launchPayBg(res){
        wx.requestPayment({
            timeStamp: res.data.response.timeStamp,
            nonceStr: res.data.response.nonceStr,
            package: res.data.response.package,
            signType: res.data.response.signType,
            paySign: res.data.response.paySign,
            success(res) {
                wx.navigateTo({
                    url: "/pages/myOrder/myOrder",
                });
                console.log('支付成功')
            },
            fail(res) {
                console.log('支付失败')
            }
        })
    }
})

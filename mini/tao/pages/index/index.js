// 获取应用实例
const app = getApp();

Page({
    data: {
        news: {},
        activity_id: 0,
        logo: './../../images/mine/yiwancheng@2x.png'
    },
    onLoad(query) {
        var activity_id = query.activity_id;
        if (activity_id) {
            wx.setStorageSync('activity_id', activity_id);
            //用户邀请用户成功
            if (query.invite_user_id) {
                var share_user_id = query.invite_user_id;
                wx.setStorageSync('share_user_id', share_user_id);
                this.addInviteUser(activity_id, share_user_id);
            }
            this.toActivityDetail(activity_id);
        }

        // scene 需要使用 decodeURIComponent 才能获取到生成二维码时传入的 scene
        const scene = decodeURIComponent(query.scene);
        if (scene !== 'undefined') {
            //通过二维码进来的
            var res = scene.split(',');
            var res_length = res.length;
            var activity_id = res[0];

            this.setData({
                activity_id: activity_id,
            });
            wx.setStorageSync('activity_id', activity_id);

            if (activity_id == 9999999) {
                //999999，表示是用来设置老师为A用户的
                app.apiRequest({
                    url: '/user/set-a',
                    method: 'get',
                    data: {},
                    success: res => {
                        wx.showToast({
                            title: '您已经成为A用户',
                            icon: 'success', //图标，支持"success"、"loading"
                        })
                    }
                });
            } else if (activity_id == 9999998) {
                //999998,加入战队
                var team_id = res[1];
                app.apiRequest({
                    url: '/activity/jointeam/' + team_id,
                    method: 'get',
                    data: {},
                    success: res => {
                        wx.showToast({
                            title: '您已经成功加入战队',
                            icon: 'success', //图标，支持"success"、"loading"
                        })
                    }
                });
            } else {
                //用户邀请用户成功
                if (res_length >= 2) {
                    var share_user_id = res[1];
                    wx.setStorageSync('share_user_id', share_user_id);
                    this.addInviteUser(activity_id, share_user_id);
                }
                this.toActivityDetail(activity_id);
            }
        }

        this.getList();
    },

    addInviteUser(activity_id, share_user_id) {
        app.apiRequest({
            url: '/activity/invite-user',
            method: 'post',
            data: {
                'activity_id': activity_id,
                'parent_user_id': share_user_id
            },
            success: res => {
                wx.showToast({
                    title: '用户邀请',
                    icon: 'success', //图标，支持"success"、"loading"
                })
            }
        });
    },

    toDetail: function (event) {
        var id = event.currentTarget.dataset.id;
        var one = event.currentTarget.dataset.one;
        //设置全局异步缓存activity_id
        wx.setStorageSync('activity_id', id);
        this.userInfoToDetail(one, id);
    },

    toActivityDetail: function (id) {
        app.apiRequest({
            url: '/activity/type/' + id,
            method: 'get',
            data: {},
            success: res => {
                var type = res.data.response.type;
                var has_over = res.data.response.has_over;
                if (has_over == 1) {
                    wx.setStorageSync('mini_over_bg', res.data.response.mini_over_bg);
                    wx.redirectTo({
                        url: '../pass/index'
                    });
                    return false;
                } else {
                    this.userInfoToDetail(type, id);
                }
            }
        });
    },

    userInfoToDetail: function (type, id) {
        app.apiRequest({
            url: '/user/info',
            method: 'get',
            data: {
                'activity_id': id
            },
            success: res => {
                console.log(res);
                var avatar = res.data.response.avatar;
                if (type === 1) {
                    wx.setStorageSync('activity_url', '../activity/one/index?id=' + id);
                } else {
                    wx.setStorageSync('activity_url', '../activity/many/index?id=' + id);
                }
                if (!avatar) {
                    wx.redirectTo({
                        url: '../user/index'
                    });
                } else {
                    wx.redirectTo({
                        url: wx.getStorageSync('activity_url')
                    });
                }
            }
        });
    },

    getList: function () {
        app.apiRequest({
            url: '/activity/lists',
            method: 'get',
            data: {},
            success: res => {
                var that = this;
                console.log(res.data);
                that.setData({
                    news: res.data.response
                })
            }
        });
    }

});

// 获取应用实例
const app = getApp();

Page({
    data: {
        news: {}
    },
    onLoad(query) {

        // scene 需要使用 decodeURIComponent 才能获取到生成二维码时传入的 scene
        const scene = decodeURIComponent(query.scene)
        if (scene !== 'undefined') {
            console.log(scene);
            var res = scene.split(',');
            var res_length = res.length;
            var activity_id = res[0];

            //设置全局异步缓存activity_id
            wx.setStorageSync('activity_id', activity_id)

            if (res_length >= 2) {
                var share_user_id = res[1];
                app.globalData.share_user_id = share_user_id
            }
            this.toActivityDetail(activity_id);
        }
        console.log('no scene');
        this.getList();
    },

    toDetail: function (event) {
        var id = event.currentTarget.dataset.id;
        var one = event.currentTarget.dataset.one;
        //设置全局异步缓存activity_id
        wx.setStorageSync('activity_id', id)
        if (one === 1) {
            wx.redirectTo({
                url: '../activity/one/index?id=' + id
            });
        } else {
            wx.redirectTo({
                url: '../activity/many/index?id=' + id
            });
        }
    },

    toActivityDetail:function(id){
        app.apiRequest({
            url: '/activity/type/'+id,
            method: 'get',
            data: {},
            success: res => {
                var type = res.data.response;
                if (type === 1) {
                    wx.redirectTo({
                        url: '../activity/one/index?id=' + id
                    });
                } else {
                    wx.redirectTo({
                        url: '../activity/many/index?id=' + id
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

var app = getApp();

// pages/activity/createActivity/index.js
Page({

    /**
     * 页面的初始数据
     */
    data: {
        info: ''
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad(options) {
        // this.getBasicInfo();
    },

    getBasicInfo() {
        var that = this;
        app.apiRequest({
            url: '/basic/settings',
            method: 'get',
            data: {
            },
            success: res => {
                that.setData({
                    'info': res.data.response
                });
            }
        });
    },

    doComplaint(e) {
        var activity_id = wx.getStorageSync('activity_id');
        var content = e.detail.value.content;
        var mobile = e.detail.value.mobile;
        var type = e.detail.value.type;
        if (content) {
            this.setData({
                isShowDislogue: false
            }),
                wx.showToast({
                    title: '加载中',
                    icon: 'loading', //图标，支持"success"、"loading"
                }),
                app.apiRequest({
                    url: '/activity/complaint',
                    method: 'post',
                    data: {
                        'activity_id': activity_id,
                        'type': type,
                        'content': content,
                        'mobile': mobile
                    },
                    success: res => {
                        wx.showToast({
                            title: ''
                        });
                    }
                });
        } else {
            wx.showToast({
                title: '请填写投诉描述',
                icon: "error"
            })
        }
    },

    /**
     * 生命周期函数--监听页面初次渲染完成
     */
    onReady() {

    },

    /**
     * 生命周期函数--监听页面显示
     */
    onShow() {

    },

    /**
     * 生命周期函数--监听页面隐藏
     */
    onHide() {

    },

    /**
     * 生命周期函数--监听页面卸载
     */
    onUnload: function () {
        let activity_url = wx.getStorageSync('activity_url');
        wx.reLaunch({
            url: activity_url,
        })
    },

    /**
     * 页面相关事件处理函数--监听用户下拉动作
     */
    onPullDownRefresh() {

    },

    /**
     * 页面上拉触底事件的处理函数
     */
    onReachBottom() {

    },

    /**
     * 用户点击右上角分享
     */
    onShareAppMessage() {

    }
})
// pages/myAward/myAward.js
const app = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        info: [],
        my_info: [],
        //当前打开的swiper
        currentIndex: 0
    },

    getAward(e) {
        var id = e.currentTarget.dataset.index;
        console.log(id);
        app.apiRequest({
            url: '/award/create',
            method: 'post',
            data: {
                'activity_id': wx.getStorageSync('activity_id'),
                'id': id
            },
            success: res => {
                console.log(res.data.msg_code);

                if (res.data.msg_code == 100000) {
                    wx.showToast({
                        title: '成功',
                        icon: 'success',
                        duration: 2000
                    });
                    this.getAwardlist();
                } else {
                    wx.showToast({
                        title: res.data.message,
                        icon: 'error',
                        duration: 2000
                    });
                }

            }
        });
    },

    changeScrollView(e) {
        //改变当前值
        this.setData({
            currentIndex: e.currentTarget.dataset.index
        })
    },
    bindchangeSwiper(e) {
        this.setData({
            currentIndex: e.detail.current
        })
        console.log(e.detail.current)
        if (e.detail.current == 1) {
            this.getMyAwardlist();
        }
    },
    //全部奖励列表
    getAwardlist: function () {
        var activity_id = wx.getStorageSync('activity_id');
        app.apiRequest({
            url: '/award/lists',
            method: 'post',
            data: {
                'activity_id': activity_id
            },
            success: res => {
                var that = this;
                that.setData({
                    info: res.data.response
                })
            }
        });
    },
    //我的奖励列表
    getMyAwardlist: function () {
        var activity_id = wx.getStorageSync('activity_id');
        app.apiRequest({
            url: '/award/my-lists',
            method: 'post',
            data: {
                'activity_id': activity_id
            },
            success: res => {
                var that = this;
                that.setData({
                    my_info: res.data.response
                })
            }
        });
    },
    /**
     * 生命周期函数--监听页面加载
     */
    onLoad(options) {
        this.getAwardlist();
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
    onUnload() {

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
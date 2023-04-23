// pages/activity/one/index.js
var app = getApp();
Page({

    /**
     * 页面的初始数据
     */
    data: {
        fields: [],
        isShowDislogue: false,//弹窗是否显示
        info: {},
        nowDate: '2021-12-22 18:00:00', //结束时间
        countdown: '', //倒计时
        days: '00', //天
        hours: '00', //时
        minutes: '00', //分
        seconds: '00', //秒
        info1: '',
        info2: '',
        name: '',
        phoneNum: '',
        imgUrls: [],
        contentImgUrls: [],
        indicatorDots: true,
        autoplay: true,
        interval: 3000,
        duration: 1000
    },

    doPay(e) {
        console.log(e, '下单');
        var activity_id = wx.getStorageSync('activity_id');
        var info = e.detail.value;
        wx.showToast({
            title: '加载中',
            icon: 'loading', //图标，支持"success"、"loading"
        }),
            //生成订单
            app.apiRequest({
                url: '/pay/pay',
                method: 'post',
                data: {
                    'activity_id': activity_id,
                    'type': 1,//1开团 2单独购买
                    'info': JSON.stringify(info),
                },
                success: res => {
                    //隐藏加载界面
                    wx.hideLoading();
                    console.log(res);
                    let code = res.data.msg_code;
                    if (code == 100000) {
                        //调起支付组件
                        app.launchPayBg(res);
                    } else {
                        wx.showModal({
                            title: '出错',
                            content: res.data.message,
                            showCancel: true,//是否显示取消按钮
                            cancelText: "否",//默认是“取消”
                            cancelColor: 'skyblue',//取消文字的颜色
                            confirmText: "是",//默认是“确定”
                            confirmColor: 'skyblue',//确定文字的颜
                        })
                    }
                }
            });
    },

    toGroupList() {
        app.toOneGroupList();
    },

    //info数据的获取
    getActivityDetail: function (activity_id) {
        var that = this;
        app.apiRequest({
            url: '/activity/detail/' + activity_id,
            method: 'get',
            data: {},
            success: res => {

                that.setData({
                    info: res.data.response,
                    nowDate: res.data.response.end_time,
                    imgUrls: JSON.parse(res.data.response.bg_banner),
                    fields: res.data.response.fields,
                    contentImgUrls: JSON.parse(res.data.response.content),
                });
                wx.setStorageSync('activity_fields', res.data.response.fields)
                this.countTime();
                app.backmusic(res.data.response.music_url);

            }
        });
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function (options) {
        this.getActivityDetail(wx.getStorageSync('activity_id'));
    },
    handletoCourseOne() {
        wx.setStorageSync('activity_type', 1)
        //立即参团
        wx.navigateTo({
            url: "/pages/activity/group/index",
        })
    },
    handletoCourseTwo() {
        this.setData({
            isShowDislogue: true
        })
        //立即开团
        console.log("开团")
    },

    countTime() {
        var that = this;
        var days, hours, minutes, seconds;
        var nowDate = that.data.nowDate;
        var now = new Date().getTime();
        var end = new Date(nowDate).getTime(); //设置截止时间
        // console.log("开始时间：" + now, "截止时间:" + end);
        var leftTime = end - now; //时间差                       
        if (leftTime >= 0) {
            days = Math.floor(leftTime / 1000 / 60 / 60 / 24);
            hours = Math.floor(leftTime / 1000 / 60 / 60 % 24);
            minutes = Math.floor(leftTime / 1000 / 60 % 60);
            seconds = Math.floor(leftTime / 1000 % 60);
            seconds = seconds < 10 ? "0" + seconds : seconds
            minutes = minutes < 10 ? "0" + minutes : minutes
            hours = hours < 10 ? "0" + hours : hours
            that.setData({
                countdown: days + ":" + hours + "：" + minutes + "：" + seconds,
                days,
                hours,
                minutes,
                seconds
            })
            // console.log(that.data.countdown)
            //递归每秒调用countTime方法，显示动态时间效果
            setTimeout(that.countTime, 1000);
        } else {
            that.setData({
                countdown: '已截止'
            })
        }
    },

    handleClosed() {
        this.setData({
            isShowDislogue: false
        })
    },

    /**
     * 生命周期函数--监听页面初次渲染完成
     */
    onReady() {

    },

    /**
     * 生命周期函数--监听页面隐藏
     */
    onHide() {

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

    onShow() {
        wx.hideHomeButton();
        app.addViewNum();
        app.setMiniProgramShareCache();
    },

    onUnload() {
        let activity_url = wx.getStorageSync('activity_url');
        wx.reLaunch({
            url: activity_url,
        })
    },

    onShareAppMessage: function (options) {
        var shareObj = app.newShareObj(options);
        return shareObj;
    }
})
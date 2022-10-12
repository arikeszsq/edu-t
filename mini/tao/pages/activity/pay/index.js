// pages/activity/pay/index.js
const app = getApp();
Page({

    /**
     * 页面的初始数据
     */
    data: {
        userInfo: {
            name: "",
            phoneNumber: "",
            age: "",
            male: "male",
            isAgree: true
        }
    },
    handlechange(e) {
        console.log(e.detail.value);
        const reg = /^1[3-9]\d{9}$/
        console.log(reg.test(e.detail.value.trim()));
        if (!reg.test(e.detail.value)) {
            // 不满足
            wx.showToast({
                title: '手机号不正确',
                icon: 'error',
                duration: 2000
            })
        }
    },
    handleMaleChange(e) {
        this.data.userInfo.male = e.detail.value
        this.setData({
            userInfo: this.data.userInfo
        })
        console.log(this.data.userInfo)
    },
    doPay() { 

    },
    /**
     * 生命周期函数--监听页面加载
     */
    onLoad(options) {
        console.log(app.globalData.activity_id, "sss")
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
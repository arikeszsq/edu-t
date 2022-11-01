// pages/activity/pay/index.js
const app = getApp();
Page({

    /**
     * 页面的初始数据
     */
    data: {
        userInfo: {
            activity_id: "",
            sign_name: "",
            sign_mobile: "",
            sign_age: "",
            sign_sex: true,
            is_agree: true
        },
        courseInfo: {}
    },

    //手机号码
    handlechange(e) {
        const reg = /^1[3-9]\d{9}$/
        if (!reg.test(e.detail.value)) {
            // 不满足
            wx.showToast({
                title: '需填正确的号码',
                icon: 'error',
                duration: 2000
            })
        } else {
            this.setData({
                "userInfo.sign_mobile": e.detail.value
            })
            console.log(this.data.userInfo)
        }
    },
    //姓名
    handleNameInput(e) {
        this.setData({
            "userInfo.sign_name": e.detail.value
        })
    },
    //年龄
    handleAgeInput(e) {
        this.setData({
            "userInfo.sign_age": e.detail.value
        })
    },
    //协议
    handleAgreement(e) {
        this.setData({
            "userInfo.is_agree": !!e.detail.value.length
        })
    },
    /**
     * 性别
     * @param {*} e 
     */
    handleMaleChange(e) {
        console.log(e.detail.value);
        if (e.detail.value === "male") {
            console.log("s")
            this.data.userInfo.sign_sex = true
        } else {
            this.data.userInfo.sign_sex = false
        }
        this.setData({
            "userInfo.sign_sex": this.data.userInfo.sign_sex
        })
    },
    /**
     * 确认支付
     */
    doPay() {
        if (this.data.userInfo.sign_name && this.data.userInfo.sign_mobile && this.data.userInfo.sign_age && this.data.userInfo.is_agree) {
            //信息完整发起支付
            app.apiRequest({
                url: '/pay/pay',
                method: 'post',
                data: {
                    'activity_id': wx.getStorageSync('activity_id'),
                    'type': app.globalData.type,
                    'sign_name': this.data.userInfo.sign_name,
                    'sign_mobile': this.data.userInfo.sign_mobile,
                    'sign_age': this.data.userInfo.sign_age,
                    'sign_sex': this.data.userInfo.sign_sex,
                    'is_agree': this.data.userInfo.is_agree,
                    'course_ids': app.globalData.selectedSchoolId.join(','),
                    'school_child_ids': app.globalData.selectedCourse.join(',')
                },
                success: res => {
                    console.log(res);
                }
            });



        } else {
            //信息不完整
            wx.showToast({
                title: '信息不完整',
                icon: 'error',
                duration: 2000
            })
        }
        console.log(this.data.userInfo, "sssss")
    },
    /**
     * 生命周期函数--监听页面加载
     */
    onLoad(options) {

        this.getSelectCourseInfo();

        this.setData({
            "data.activity_id": wx.getStorageSync('activity_id')
        })
    },

    getSelectCourseInfo() {
        app.apiRequest({
            url: '/course/courseschool/info',
            method: 'post',
            data: {
                'course_ids': app.globalData.selectedSchoolId.join(','),
                'school_ids': app.globalData.selectedCourse.join(',')
            },
            success: res => {
                console.log(res);
                this.setData({
                    courseInfo: res.data.response
                })
            }
        });
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
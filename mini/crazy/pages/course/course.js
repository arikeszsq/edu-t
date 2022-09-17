// pages/course/course.js
const app = getApp();
Page({

    /**
     * 页面的初始数据
     */
    data: {
        type: "",
        //家的地址
        adressObj: {
            latitude: '',
            longitude: '',
            name: '',
        },
        CourseCategroy: {},

        indexSize: 0, // 当前点击
        indicatorDots: false, // 是否显示面板指示点
        autoplay: false,  // 是否自动切换
        duration: 0,  // 滑动动画时长
        detail: [
            { "children": [{ "id": "14", "name": "办公", }, { "id": "13", "name": "形象与礼仪", }], "id": "1", "name": "通用课程", },
            { "children": [{ "id": "24", "name": "营销类", }, { "id": "23", "name": "行政类", }, { "id": "22", "name": "财务类", }, { "id": "21", "name": "人力资源类", }], "id": "2", "name": "专业知识", },
            { "children": [{ "id": "33", "name": "领导力", }, { "id": "32", "name": "基层管理", }, { "id": "31", "name": "GSP管理", }], "id": "3", "name": "管理课程", },
            { "children": [{ "id": "44", "name": "营销类", }, { "id": "43", "name": "行政类", }, { "id": "42", "name": "财务类", }, { "id": "41", "name": "人力资源类", }], "id": "4", "name": "制度政策", },] // 分类集合
    },

    /**
     * 一级分类选择
     */
    chooseTypes: function (e) {
        this.setData({
            indexSize: e.target.dataset.index
        })
    },


    /**
     * 二级分类选择
     */
    chooseSecondMenu: function (e) {
        let cId = e.target.dataset.cid;
        let cName = e.target.dataset.cname;
        wx.showModal({
            title: '请确认上课院校',
            content: '点击选择了：' + cId + '-' + cName,
            showCancel: false,
            success(res) {
            }
        })
    },


    //地图设置家的位置
    setHomeAdress() {
    wx.navigateTo({
        url: "/pages/position/position",
    })
},
    searchNearOrgan() {
    wx.navigateTo({
        url: "/pages/checkSchool/checkSchool",
    })
},

    //课程分类的获取
    getCourseCategroy: function () {
        app.apiRequest({
            url: '/course/lists',
            method: 'get',
            data: {
                'activity_id':1
            },
            success: res => {
                var that = this;
                console.log(res.data.response)
                that.setData({
                    CourseCategroy: res.data.response
                })
            }
        });
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad(options) {
    this.getCourseCategroy();
    var buy_type = wx.getStorageSync('buy_type');
    console.log(buy_type)
        // 家的位置的设置
        const obj = wx.getStorageSync('trueCity')
        if(obj.latitude) {
    console.log('adressObj:' + JSON.stringify(obj));
    this.setData({
        adressObj: obj
    })
    return false;
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
onShow: function () {
    //设置家的地址修改之后
    const obj = wx.getStorageSync('trueCity');
    if (this.data.flag) {
        if (obj.longitude != this.data.adressObj.longitude || obj.latitude != this.data.adressObj.latitude) {
            console.log('adressObj:' + JSON.stringify(obj));
            this.setData({
                adressObj: obj,
                index: 0
            });

        }
    }
    this.setData({
        flag: true
    })

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
// pages/activity/group/index.js
var app = getApp();
Page({

    /**
     * 页面的初始数据
     */
    data: {
        // 默认显示向上按钮
        isTopArrow: true,
        isExTand: "",
        groupLists: [],
        groupOneList: [],

    },
    searchFocus() {
        console.log("聚集")
    },
    /**
     * 二级菜单显示的处理
     */
    handleSubShow(e) {
        const id = e.currentTarget.dataset.index;
        //第二次点击
        if (this.data.isExTand === id) {
            this.setData({
                isExTand: ""
            })
            return
        }

        this.setData({
            isExTand: id,
            //清楚之前的数据
            groupOneList: []
        })
        //渲染最新的数据
        this.getGroupList(id);
    },
    /**
     * 立即参与
     */
    handleSucessGroup() {
        wx.navigateTo({
            url: '/pages/activity/pay/index',
        })
    },
    // 团列表数据获取
    getGroupLists(id) {
        app.apiRequest({
            url: '/group/lists',
            method: 'get',
            data: {
                "activity_id": id
            },
            success: res => {
                var that = this;
                console.log(res.data.response, "列表数据");
                that.setData({
                    groupLists: res.data.response,
                })
            }
        });
    },

    //参团的用户列表
    getGroupList(gruop_id) {
        app.apiRequest({
            url: '/group/user-lists' + "/" + gruop_id,
            method: 'get',
            data: {
            },
            success: res => {
                var that = this;
                console.log(res.data.response, "单个团的");
                that.setData({
                    groupOneList: res.data.response,
                })
            }
        });
    },
    /**
     * 生命周期函数--监听页面加载
     */
    onLoad(options) {
        this.getGroupLists(1);

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
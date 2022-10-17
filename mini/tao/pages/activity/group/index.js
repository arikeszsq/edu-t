// pages/activity/group/index.js
var app = getApp();
Page({

    /**
     * 页面的初始数据
     */
    data: {
        name: "",
        phoneNum: "",
        //第一个下拉选框
        conditionList: [{
            title: '选项1',
            id: '1',
            select: true
        },
        {
            title: '选项2',
            id: '2',
            select: false
        },
        {
            title: '选项3',
            id: '3',
            select: false
        },
        {
            title: '选项4',
            id: '4',
            select: false
        },
        {
            title: '选项5',
            id: '5',
            select: false
        }
        ],
        choosedCondition: {
            title: '请选择',
            id: '1'
        },
        conditionVisible: false,
        //第二个下拉选框
        conditionList2: [{
            title: '选项01',
            id: '1',
            select: true
        },
        {
            title: '选项02',
            id: '2',
            select: false
        },
        {
            title: '选项03',
            id: '3',
            select: false
        },
        {
            title: '选项04',
            id: '4',
            select: false
        },
        {
            title: '选项04',
            id: '5',
            select: false
        }
        ],
        choosedCondition2: {
            title: '请选择',
            id: '1'
        },
        conditionVisible2: false,

        // 默认显示向上按钮
        isTopArrow: true,//列表箭头的显示
        isExTand: "",//需要展开二级的数据的id
        groupLists: [],//列表数据
        groupOneList: [],//列表二级数据
        groupId: "",//参团的id
        isShowDislogue: false,//弹窗是否显示

    },
    //弹窗的name
    handleName(e) {
        this.setData({
            name: e.detail.value
        })
    },
    //弹窗的phoneNum
    handlephoneNum(e) {
        const reg = /^1[3-9]\d{9}$/
        if (reg.test(e.detail.value.trim())) {
            this.setData({
                phoneNum: e.detail.value
            })
        } else {
            wx.showToast({
                title: '填写正确号码',
                icon: "error"
            })
        }
    },
    //弹窗的确认参团
    handleConfirm() {
        if (this.data.name && this.data.phoneNum) {
            this.setData({
                isShowDislogue: false
            })
            //跳转支付页面
            wx.navigateTo({
                url: "/pages/activity/pay/index",
            })

        } else {
            wx.showToast({
                title: '请填写信息',
                icon: "error"
            })
        }

    },

    // 弹出框的第一个下拉选框
    // 下来选框
    showCondition() {
        this.setData({
            conditionVisible: !this.data.conditionVisible
        })
    },
    // 改变查询项
    onChnageCondition(e) {
        const list = this.data.conditionList
        list.forEach(item => {
            if (item.id === e.currentTarget.dataset.id) {
                item.select = true
                this.setData({
                    'choosedCondition.title': item.title,
                    'choosedCondition.id': item.id
                })
            } else {
                item.select = false
            }
        })
        this.setData({
            conditionList: list
        })
    },
    // 弹出框的第二个下拉选框
    // 下来选框
    showCondition2() {
        this.setData({
            conditionVisible2: !this.data.conditionVisible2
        })
    },
    // 改变查询项
    onChnageCondition2(e) {
        const list = this.data.conditionList2
        list.forEach(item => {
            if (item.id === e.currentTarget.dataset.id) {
                item.select = true
                this.setData({
                    'choosedCondition2.title': item.title,
                    'choosedCondition2.id': item.id
                })
            } else {
                item.select = false
            }
        })
        this.setData({
            conditionList2: list
        })
    },
    handleClosed() {
        //关闭弹窗,清楚id的数据
        this.setData({
            groupId: "",
            isShowDislogue: false
        })
    },

    //以上为弹出框的处理函数

    //搜索栏目
    searchFocus() {
        console.log("聚集")
    },
    /**
     * 列表二级菜单显示的处理
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
    handleSucessGroup(e) {
        //需要发送给后端
        const id = e.currentTarget.dataset.id
        this.setData({
            groupId: id,
            isShowDislogue: true
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
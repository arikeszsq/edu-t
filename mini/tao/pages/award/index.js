// pages/award/index.js
Page({

    /**
     * 页面的初始数据
     */
    data: {
        canAwardSelectedNum:1,
        awardList: [
            {
                id: 1,
                imgUrl: 'https://img1.baidu.com/it/u=348861822,4093687718&fm=253&fmt=auto&app=138&f=JPEG?w=889&h=500',
                title: 'CHN',
                description: '撒旦发个asfsadfasdfasd萨芬飞洒地方 ',
                checked: false
            },
            {
                id: 2,
                imgUrl: 'https://img1.baidu.com/it/u=348861822,4093687718&fm=253&fmt=auto&app=138&f=JPEG?w=889&h=500',
                title: 'CHN',
                description: '美国',
                checked: false
            },
            {
                id: 3,
                imgUrl: 'https://img1.baidu.com/it/u=348861822,4093687718&fm=253&fmt=auto&app=138&f=JPEG?w=889&h=500',
                title: 'CHN',
                description: '美国',
                checked: false
            }
        ],
        selectedIds: []
    },

    checkboxChange(e) {
        console.log('携带value值为：', e.detail.value)
        this.setData({
            'selectedIds': e.detail.value
        });
    },

    nextHandle() {
        if (this.data.selectedIds.length > this.data.canAwardSelectedNum) {
            wx.showToast({
                title: '只能选' + this.data.canAwardSelectedNum + '项',
                icon: 'error',
                duration: 2000
            });
            return;
        }
        wx.setStorageSync('award_ids', JSON.stringify(this.data.selectedIds));
        console.log(wx.getStorageSync('award_ids'), 'selectedIds');
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad(options) {
        if (!wx.getStorageSync('canAwardSelectedNum')) {
            wx.setStorageSync('canAwardSelectedNum', 1);
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
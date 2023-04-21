// pages/inviteLog/inviteLog.js
const app = getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    isSelected: 1,
    lists: [],
    signal_num: 0,
    ok_no: 0,
    no_no: 0,
  },

  clickTap: function (e) {
    var curId = e.currentTarget.dataset.id;
    this.setData({
      isSelected: curId,
    })
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    if (!wx.getStorageSync('activity_id')) {
      wx.setStorageSync('activity_id', 9)
    }
    let that = this;
    app.apiRequest({
      url: '/invite/lists',
      method: 'post',
      data: {
        'activity_id': wx.getStorageSync('activity_id'),
      },
      success: res => {
        that.setData({
          lists: res.data.response,
          signal_num: res.data.response.signal_num,
          ok_no: res.data.response.ok_no,
          no_no: res.data.response.no_no,
        })
      }
    });
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  }
})
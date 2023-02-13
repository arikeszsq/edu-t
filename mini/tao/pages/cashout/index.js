// pages/cashout/index.js
var app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {

  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

  },

  doCashOut(e) {
    var activity_id = wx.getStorageSync('activity_id');
    var apply_money = e.detail.value.apply_money;
    if (apply_money) {
      app.apiRequest({
        url: '/user/apply-cash-out',
        method: 'post',
        data: {
          'activity_id': activity_id,
          'apply_money': apply_money
        },
        success: res => {
          var code = res.data.msg_code;
          if(code==100000){
            wx.showToast({
              title: '成功,待审核'
            });
          }else{
            wx.showToast({
              title: res.data.message,
              icon:'error',
            });
          }
        }
      });
    } else {
      wx.showToast({
        title: '请填写提现金额',
        icon: "error"
      })
    }
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
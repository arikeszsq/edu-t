const app = getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    info:{},
    company:[]
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var keyName = app.globalData.userInfo.nickName;
    if(options.id){
      var id = options.id;
      wx.setStorageSync(keyName, id);
    }
    var activity_id = wx.getStorageSync(keyName);
    this.getActivityDetail(activity_id);
    console.log('activity_id:'+activity_id);
  },

  getActivityDetail:function(activity_id){
    app.apiRequest({
      url: '/activity/detail/'+activity_id,
      method: 'get',
      data: {
      },
      success: res => {
        var that = this;
        console.log(res.data.response)
        that.setData({
         info:res.data.response,
         company:res.data.response.activity_company
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
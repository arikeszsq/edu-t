// 获取应用实例
const app = getApp()

Page({
  data: {
    news: {}
  },
  onLoad() {
    this.getList();
  },
  
  toDetail:function(event){
    var id = event.currentTarget.dataset.id;
    wx.navigateTo({
      url:'../activity/detail/index?id='+id
    });
  },

  getList: function () {
    app.apiRequest({
      url: '/activity/lists',
      method: 'get',
      data: {
      },
      success: res => {
        var that = this;
        console.log(res.data)
        this.setData({
          news: res.data.response
        })
      }
    });
  }

})

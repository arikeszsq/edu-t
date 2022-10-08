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
    var one= event.currentTarget.dataset.one;
    if(one==1){
        wx.redirectTo({
            url:'../activity/one/index?id='+id
          });
    }else{
        wx.redirectTo({
            url:'../activity/many/index?id='+id
          });
    }
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
        that.setData({
          news: res.data.response
        })
      }
    });
  }

})

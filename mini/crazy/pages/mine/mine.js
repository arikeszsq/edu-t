// const util = require('../../utils/util.js');
var app = getApp();
const util = require('../../utils/util');

Page({
    data: {
        info: {}
    },
    /**
     * 组件的方法列表
     */

    onLoad: function (options) {
        this.getUserInfo();
    },
    getUserInfo: function () {
        app.apiRequest({
            url: '/user/info',
            method: 'get',
            data: {
            },
            success: res => {
                var that = this;
                console.log(res.data.response);
                that.setData({
                    info: res.data.response,
                })
            }
        });
    }
})
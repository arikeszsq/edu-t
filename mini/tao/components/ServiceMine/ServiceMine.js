const app = getApp();

// components/Service/Service.js
Component({
    /**
     * 组件的属性列表
     */
    properties: {

    },

    /**
     * 组件的初始数据
     */
    data: {
        isHaiBaoShow: false,
        img_url:''
    },

    computed: {
    },
    /**
     * 组件的方法列表
     */


    methods: {
        //获取海报
        getHaiBao() {
            this.setData({
                isHaiBaoShow: true
            });
            if (!wx.getStorageSync('activity_id')) {
                wx.setStorageSync('activity_id', 9)
            }
            var that = this;
            app.apiRequest({
                url: '/user/get-invite-pic',
                method: 'post',
                data: {
                    'activity_id': wx.getStorageSync('activity_id'),
                },
                success: res => {
                    that.setData({
                        img_url: res.data.response,
                    })
                }
            });

        },
        closedHaiBao() {
            this.setData({
                isHaiBaoShow: false
            })
        },

        //我的页面跳转
        navigatorMy() {
            wx.navigateTo({
                url: '/pages/mine/mine',
            })
        },
        //弹出客服页面
        serviceDialogue() {
            this.getKeFuDetail();
            this.setData({
                isShow: true
            })
        },
        closedHanler() {
            this.setData({
                isShow: false
            })
        },
        getKeFuDetail() {
            var that = this;
            app.apiRequest({
                url: '/basic/settings',
                method: 'get',
                data: {
                },
                success: res => {
                    that.setData({
                        kf_name: res.data.response.kf_name,
                        mobile: res.data.response.mobile,
                        pic: res.data.response.pic
                    })
                }
            });
        },
    }
})

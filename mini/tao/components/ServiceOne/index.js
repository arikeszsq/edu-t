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
        isShow: false
    },

    computed: {
    },
    /**
     * 组件的方法列表
     */


    methods: {

        //我的页面跳转
        navigatorMy() {
            wx.navigateTo({
                url: '/pages/mine/mine',
            })
        },
        //我的页面跳转
        toCourseOne(e) {
            this.triggerEvent("toCourseOne")
        },
        toCourseTwo(e) {
            this.triggerEvent("toCourseTwo")
        },
        //弹出客服页面
        serviceDialogue() {
            this.setData({
                isShow: true
            })
        },
        closedHanler() {
            this.setData({
                isShow: false
            })
        }
    }
})

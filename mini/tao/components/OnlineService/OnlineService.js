// components/OnlineService/OnlineService.js
Component({
    /**
     * 组件的属性列表
     */
    properties: {
        isShow: {
            type: Boolean,
            value: "true"
        }
    },

    /**
     * 组件的初始数据
     */
    /**
     * 组件的初始数据
     */
    data: {
        kf_name: '',
        mobile: "15062332900",
        pic: "https://zsq.a-poor.com/uploads/images/8376382dd5344e8ee76cda8ac697c909.png"
    },
    onload() {
       
    },
    /**
     * 组件的方法列表
     */
    methods: {
        closedHanler() {
            this.triggerEvent('closedHanler')
    
        },
    }
})

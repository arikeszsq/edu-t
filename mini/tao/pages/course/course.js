// pages/course/course.js
const app = getApp();
Page({
    /**
     * 页面的初始数据
     */
    data: {
        canSelectedNum: 4,
        type: "",
        //家的地址
        adressObj: {
            latitude: '',
            longitude: '',
            name: '',
        },
        CourseCategroy: [],//分类和课程列表
        schoolLists: [],//选定课程后id,获取的课程校区列表
        indexSize: 0, // 当前点击课程分类，全部默认是0
        indicatorDots: false, // 是否显示面板指示点
        autoplay: false,  // 是否自动切换
        duration: 0,  // 滑动动画时长
        dialogShow: false,
        //四个校区的集合,需要发送给后端
        schoolSelectedIds: [],
        //四个课程id,需要发后端
        courseSelectedIds: [],
        selectedTypeArray: [],//已经选择的大类数组，每个大类只能选一个

        //单个选择校区的
        selectedSchool: {},
        //一次性保存，用来修改isSelected
        tempChoosedCourseId: "",
    },

    //点击课程，获取校区列表
    getSchoolLists: function (e) {
        //第一步，判断是取消还是选择课程：是取消的则直接取消后结束代码
        const that = this;
        let id = e.currentTarget.dataset.id;//点击的课程id
        //判断这个课程id,是否在已选中的课程数组里：在的话，就是取消操作；不在的话，就是添加操作则展示校区列表

        if (this.data.courseSelectedIds.indexOf(id) !== -1) {
            //找到了，这是取消操作
            console.log('取消操作', '方式');
            //第一步，取消课程id
            this.data.courseSelectedIds.splice(this.data.courseSelectedIds.indexOf(id), 1);
            //第二步，取消大类id
            this.data.selectedTypeArray.splice(this.data.selectedTypeArray.indexOf(this.data.indexSize), 1);
            //第三步，取消校区id ,校区数组的key和课程数组的key是一样的，所有可以直接找到课程的key,从校区数组中删除校区ID
            this.data.schoolSelectedIds.splice(this.data.courseSelectedIds.indexOf(id), 1);
            //第四步，去掉选中表示的红色标记勾
            const arryCourse = this.data.CourseCategroy[this.data.indexSize].children;
            for (const item of arryCourse) {
                //根据资料里面值进行判断
                if (item.id === id && item.isSelected) {
                    //需要重新设置一下isSelected
                    item.isSelected = false;
                }
            }
            that.setData({
                courseSelectedIds: this.data.courseSelectedIds,
                schoolSelectedIds: this.data.schoolSelectedIds,
                selectedTypeArray: this.data.selectedTypeArray,
                CourseCategroy: this.data.CourseCategroy,
            })
            return;
        }

        //第二步：判断是选则课程，则判断选择课程总数是否超标
        if (that.data.schoolSelectedIds.length >= that.data.canSelectedNum) {
            wx.showToast({
                title: '只能选' + that.data.canSelectedNum + '门',
                icon: 'error',
                duration: 2000
            });
            return;
        }

        //第三步：选择大类是否超标，每个大类只能选一个
        if (this.data.selectedTypeArray.indexOf(this.data.indexSize) !== -1) {
            wx.showToast({
                title: '每大类限一项',
                icon: 'error',
                duration: 2000
            })
            return
        }

        //第四步：是选取操作，获取校区列表
        this.setData({
            tempChoosedCourseId: id,
            dialogShow: true,//显示校区选的的弹框
        })


        // 第二次点击就是需要取消课程的
        app.apiRequest({
            url: '/course/company-child-lists/' + id,
            method: 'get',
            data: {
            },
            success: res => {
                var that = this;
                that.setData({
                    schoolLists: res.data.response
                })
            }
        });
    },
    nextHandle() {
        let canSelectedNum=this.data.canSelectedNum;
        const length = this.data.schoolSelectedIds.length
        if (length < canSelectedNum) {
            wx.showToast({
                title: `还需要选择${canSelectedNum - length}个课程`,
            })
            return
        } else {
            //课程储存到缓存里去
            //课程得id
            wx.setStorageSync('courseSelectedIds', this.data.courseSelectedIds);
            console.log(wx.getStorageSync('courseSelectedIds'),'courseSelectedIds');
            //校区得id
            wx.setStorageSync('schoolSelectedIds', this.data.schoolSelectedIds);
            console.log(wx.getStorageSync('schoolSelectedIds'),'schoolSelectedIds');
            wx.navigateTo({
                url: '/pages/award/index',
            })
        }
    },
    //辅助选中的函数
    isSelectedCourse(id, isSelected) {
        //////////////////////////////////////////////////////需要解决的问题
        //问题？？如何在总的分类里面选择，下面分类里面也需要选择
        const Choosechildren = this.data.CourseCategroy[this.data.indexSize].children
        for (const item of Choosechildren) {
            if (item.id === id) {
                item.isSelected = isSelected
            }
        }
        this.setData({
            CourseCategroy: this.data.CourseCategroy
        })
    },


    //选择校区的处理函数
    chooseSchoolHandle(e) {
        //选择的校区数据的展示
        let item = e.currentTarget.dataset.item;
        this.setData({
            selectedSchool: item
        })
    },
    handleCancel() {
        this.setData({
            dialogShow: false
        })
    },
    handleComfire() {
        // 需要判断一下是否选择了，没有选择就需要提示
        if (Object.keys(this.data.selectedSchool).length == 0) {
            wx.showToast({
                title: '请选择校区',
                icon: 'error',
                duration: 2000
            })
            return
        }
        //集合schoolSelectedIds，选中的id
        this.data.schoolSelectedIds.push(this.data.selectedSchool.id);
        this.data.courseSelectedIds.push(this.data.tempChoosedCourseId);
        this.data.selectedTypeArray.push(this.data.indexSize);

        this.setData({
            schoolSelectedIds: this.data.schoolSelectedIds,
            courseSelectedIds: this.data.courseSelectedIds,
            selectedTypeArray: this.data.selectedTypeArray,
            dialogShow: false,
            selectedSchool: {}
        })
        // console.log(this.data.schoolSelectedIds, "id");
        //设置列表页面的选中状态
        this.isSelectedCourse(this.data.tempChoosedCourseId, true)
    },

    /**
     * 一级分类选择
     */
    chooseTypes: function (e) {
        console.log(e.target.dataset.index, 'indexSize');//当前选中的大类
        this.setData({
            indexSize: e.target.dataset.index,
        })
    },

    //地图设置家的位置
    setHomeAdress() {
        wx.navigateTo({
            url: "/pages/position/position",
        })
    },
    searchNearOrgan() {
        wx.navigateTo({
            url: "/pages/checkSchool/checkSchool",
        })
    },

    //课程分类的获取
    getCourseCategroy: function () {
        app.apiRequest({
            url: '/course/lists',
            method: 'get',
            data: {
                'activity_id': wx.getStorageSync('activity_id')
            },
            success: res => {
                var that = this;
                //给children里面加isSelectd属性
                for (const item of res.data.response) {
                    for (const iterator of item.children) {
                        iterator.isSelected = false
                        console.log(iterator, "1sss")
                    }
                }
                console.log(res.data.response, "ddd")
                that.setData({
                    CourseCategroy: res.data.response
                })
            }
        });
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad(options) {
        if (!wx.getStorageSync('canSelectedNum')) {
            wx.setStorageSync('canSelectedNum', 4);
        }else{
            this.setData({
                canSelectedNum: wx.getStorageSync('canSelectedNum')
            })
        }
        if (!wx.getStorageSync('activity_id')) {
            wx.setStorageSync('activity_id', 9);
        }
        this.getCourseCategroy();
        var buy_type = wx.getStorageSync('buy_type');
        // 家的位置的设置
        const obj = wx.getStorageSync('trueCity');
        if (obj.latitude) {
            console.log('adressObj:' + JSON.stringify(obj));
            this.setData({
                adressObj: obj
            })
            return false;
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
    onShow: function () {
        //设置家的地址修改之后
        const obj = wx.getStorageSync('trueCity');
        if (this.data.flag) {
            if (obj.longitude != this.data.adressObj.longitude || obj.latitude != this.data.adressObj.latitude) {
                console.log('adressObj:' + JSON.stringify(obj));
                this.setData({
                    adressObj: obj,
                    index: 0
                });
            }
        }
        this.setData({
            flag: true
        })
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

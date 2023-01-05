<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <script src="/vue/vue.js"></script>
    <script src="/vue/axios/axios.min.js"></script>
    <script src="/vue/element-ui/index.js"></script>
    <link rel="stylesheet" href="/vue/element-ui/index.css">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>


<div id="app">

    <el-row :gutter="12">
        <el-col :span="8">
            <el-card shadow="always">
                总是显示
            </el-card>
        </el-col>
        <el-col :span="8">
            <el-card shadow="hover">
                鼠标悬浮时显示
            </el-card>
        </el-col>
        <el-col :span="8">
            <el-card shadow="never">
                从不显示
            </el-card>
        </el-col>
    </el-row>


    <el-table
        :data="tableData"
        border
        style="width: 100%">
        <el-table-column
            prop="date"
            label="日期"
            width="180">
        </el-table-column>
        <el-table-column
            prop="name"
            label="姓名"
            width="180">
        </el-table-column>
        <el-table-column
            prop="address"
            label="地址">
        </el-table-column>
    </el-table>

</div>

<script>
    new Vue({
        el: '#app',
        data: function () {
            return {
                tableData: [{
                    date: '2016-05-02',
                    name: '王小虎',
                    address: '100'
                }, {
                    date: '2016-05-04',
                    name: '王小虎',
                    address: 200
                }, {
                    date: '2016-05-01',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1519 弄'
                }, {
                    date: '2016-05-03',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1516 弄'
                }],
                total:60
               }
        },
        methods: {

            handleRemove(file, fileList) {
                console.log(file, fileList);
            },
            handlePreview(file) {
                console.log(file);
            },
            handleExceed(files, fileList) {
                this.$message.warning(`当前限制选择 3 个文件，本次选择了 ${files.length} 个文件，共选择了 ${files.length + fileList.length} 个文件`);
            },
            beforeRemove(file, fileList) {
                return this.$confirm(`确定移除 ${ file.name }？`);
            },

            onSubmit() {
                this.newOrder();
            },
            newOrder() {
                var that = this;
                that.loading=true;
                var fd = new FormData();
                fd.append('params', JSON.stringify(that.dynamicValidateForm));
                var config = {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                };
                var baseUrl = '/labour/protocalorder/ajaxAdd';
                axios.post(baseUrl, fd, config
                ).then(res => {
                    if (res.data.code === 200) {
                        $res_type = 'success';
                        that.dynamicValidateForm.order_id = res.data.order_id;
                    } else {
                        $res_type = 'error';
                    }
                    that.loading=false;
                    that.$message({
                        type: $res_type,
                        message: res.data.msg
                    });
                }).catch(res => {
                    that.loading=false;
                    console.log(res)
                });
            },
        }
    })
</script>

</body>
</html>

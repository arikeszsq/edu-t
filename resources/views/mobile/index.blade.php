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
</head>
<body>
<div id="app">
    <el-form ref="form" :model="form" label-width="80px" label-position="top">
        <el-row :gutter="10">
            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                <el-form-item label="内容">
                    <el-input type="textarea" v-model="content"></el-input>
                </el-form-item>
            </el-col>
            <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                <el-form-item>
                    <el-button type="primary" round @click="onSubmit(1)">吃</el-button>
                </el-form-item>
            </el-col>
            <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                <el-form-item>
                    <el-button type="success" round @click="onSubmit(2)">喝</el-button>
                </el-form-item>
            </el-col>
            <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                <el-form-item>
                    <el-button type="danger" round @click="onSubmit(3)">拉</el-button>
                </el-form-item>
            </el-col>
            <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                <el-form-item>
                    <el-button type="warning" round @click="onSubmit(4)">撒</el-button>
                </el-form-item>
            </el-col>
        </el-row>
    </el-form>
</div>
<script>
    new Vue({
        el: '#app',
        data: function () {
            return {
                content: '',
                form: {
                    desc: ''
                }
            }
        },
        methods: {
            onSubmit(type) {
                var that = this;
                that.loading = true;
                var fd = new FormData();
                fd.append('type', type);
                fd.append('content', this.content);
                var config = {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                };
                var baseUrl = 'api/index/add-content';
                axios.post(baseUrl, fd, config
                ).then(res => {
                    if (res.data.code == 200) {
                        that.$message.success('成功')
                    }else{
                        that.$message.error('失败');
                    }
                }).catch(res => {
                    that.loading = false;
                    console.log(res)
                });
            },
        }
    })
</script>
</body>
</html>

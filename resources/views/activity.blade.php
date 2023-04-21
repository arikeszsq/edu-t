<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>活动管理</title>

<script src="/vue/vue.js"></script>
<script src="/vue/axios/axios.min.js"></script>
<script src="/vue/element-ui/index.js"></script>
<link rel="stylesheet" href="/vue/element-ui/index.css">
<input class="hidden" id="type" value="{{ $type }}">
<div id="app_vue">

    <h1>{{$name}}</h1>
    <el-row :gutter="10" style="margin-bottom: 20px;">
        <el-col :xs="8" :sm="12" :md="6" :lg="4" :xl="1">
            <el-input v-model="name" placeholder="请输入活动名称"></el-input>
        </el-col>
        <el-col :xs="4" :sm="6" :md="8" :lg="9" :xl="11">
            <el-select v-model="status" clearable placeholder="请选择">
                <el-option label="上架" value="1">上架</el-option>
                <el-option label="下架" value="2">下架</el-option>
            </el-select>
            <el-button type="success" plain>检索</el-button>
        </el-col>
        <el-col :xs="4" :sm="6" :md="8" :lg="9" :xl="11">
            <a :href="new_url">
                <el-button type="primary">+新增</el-button>
            </a>
        </el-col>
    </el-row>

    <div class="table-list">
        <el-table
            :data="tableData"
            border
            style="width: 100%">
            <el-table-column
                fixed
                prop="name"
                label="活动名称"
                width="200">
            </el-table-column>
            <el-table-column
                prop="start_time"
                label="开始时间"
                width="150">
            </el-table-column>

            <el-table-column
                prop="start_time"
                label="结束时间"
                width="150">
            </el-table-column>

            <el-table-column
                prop="start_time"
                label="库存"
                width="150">
            </el-table-column>

            <el-table-column
                prop="start_time"
                label="浏览数"
                width="150">
            </el-table-column>

            <el-table-column
                prop="start_time"
                label="成功报名"
                width="150">
            </el-table-column>

            <el-table-column
                prop="start_time"
                label="退款订单量"
                width="150">
            </el-table-column>

            <el-table-column
                prop="start_time"
                label="退款金额"
                width="150">
            </el-table-column>

            <el-table-column
                prop="start_time"
                label="总付款金额"
                width="150">
            </el-table-column>

            <el-table-column
                prop="start_time"
                label="已返利总金额"
                width="150">
            </el-table-column>

            <el-table-column
                prop="start_time"
                label="净利润"
                width="150">
            </el-table-column>

            <el-table-column
                        prop="pic"
                        header-align="center"
                        align="center"
                        label="活动二维码">
                <template
                 slot-scope="scope">
                <el-popover
                    placement="right"
                    trigger="click">
                    <img :src="scope.row.pic" width="600px">
                    <img slot="reference" :src="scope.row.pic" width="60px" height="60px">
                </el-popover>
                </template>
            </el-table-column>

            <el-table-column
                prop="start_time"
                label="上下架状态">
            </el-table-column>


            <el-table-column
                fixed="right"
                label="操作"
                width="300" v-if="type==1">
                <template slot-scope="scope">
                    <el-button @click="handleClick(scope.row)" type="text" size="small">查看</el-button>
                    <el-button type="text" size="small">编辑</el-button>
                </template>
            </el-table-column>

            <el-table-column
                fixed="right"
                label="操作"
                width="300" v-if="type==2">
                <template slot-scope="scope">
                    <el-button @click="handleClick(scope.row)" type="text" size="small">查看</el-button>
                    <el-button type="text" size="small">编辑</el-button>
                </template>
            </el-table-column>


        </el-table>
        <el-row :gutter="20">
            <el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
                <div class="block">
                    <el-pagination
                        @size-change="handleSizeChange"
                        @current-change="handleCurrentChange"
                        :current-page="current_page"
                        :page-sizes="[10, 15, 20,25,30]"
                        :page-size="page_size"
                        layout="total, sizes, prev, pager, next, jumper"
                        :total="total">
                    </el-pagination>
                </div>
            </el-col>
        </el-row>
    </div>
</div>
        
</div>
<script>
    new Vue({
        el: '#app_vue',
        data: function () {
            return {
                new_url: '/admin/activity-one/create',
                type: $('#type').val(),
                name: '',
                status: '',
                current_page: 1,
                page_size: 10,
                total: 10,
                tableData: [
                    {
                        'name': 'asf',
                        'start_time': 'asf',
                        'pic': 'https://img69.imagetwist.com/th/55553/sf801t70x1vl.jpg',
                    }
                ],
                dialogVisible: false,
                previewpic: ""
            }
        },
        methods: {
            previewPic(url) {
                this.previewpic = url;
                this.dialogVisible = true;
            },
            search(start, end) {

            },
            init() {
                if (this.type == '2') {
                    this.new_url = '/admin/activity-many/create';
                }
                this.search(0, 10);
            },
            handleClick(row) {
                console.log(row);
            },

            /*** 改变当前页显示数据条数 **/
            handleSizeChange(val) {
                this.page_size = val;
                var start = (this.current_page - 1) * this.page_size;
                var end = this.current_page * this.page_size;
                this.search(start, end);
            },
            /*** 挑战到第几页 **/
            handleCurrentChange(val) {
                this.current_page = val;
                var start = (this.current_page - 1) * this.page_size;
                var end = this.current_page * this.page_size;
                this.search(start, end);
            },
        },
        created: function () {
            this.init();
        }
    })

</script>



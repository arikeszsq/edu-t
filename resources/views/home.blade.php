<div class="row">
    <div class="col-md-12"><h3>客户统计</h3></div>
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3 style="color: white;">{{$total_money}}</h3>
                <p>总资产</p>
            </div>
            <div class="icon">
                <i class="fa fa-info-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3 style="color: white;">{{$total_user}}</h3>
                <p>客户总数</p>
            </div>
            <div class="icon">
                <i class="fa fa-info-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3 style="color: white;">{{$total_pay_user_num}}</h3>
                <p>下单客户总数</p>
            </div>
            <div class="icon">
                <i class="fa fa-info-circle"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12"><h3>传播统计</h3></div>
    <div class="col-md-12">
        <ul class="list-group list-group-horizontal">
            <li class="list-group-item list-group-item-info" style="width: 150px;">今日浏览人数</li>
            <li class="list-group-item" style="width: 100px; margin-right: 20px;">{{$total_user_today_view}}</li>

            <li class="list-group-item list-group-item-info" style="width: 150px;">今日报名人数</li>
            <li class="list-group-item" style="width: 100px; margin-right: 20px;">{{$total_user_today_sign}}</li>

            <li class="list-group-item list-group-item-info" style="width: 150px;">今日分享人数</li>
            <li class="list-group-item" style="width: 100px; margin-right: 20px;">{{$total_user_today_share}}</li>

            <li class="list-group-item list-group-item-info" style="width: 150px;">今日付款金额</li>
            <li class="list-group-item" style="width: 100px; margin-right: 20px;">{{$total_user_today_pay}}</li>
        </ul>
        <ul class="list-group list-group-horizontal-sm" style="margin-top: 10px;">
            <li class="list-group-item list-group-item-info" style="width: 150px;">总浏览人数</li>
            <li class="list-group-item" style="width: 100px; margin-right: 20px;">{{$total_user_view}}</li>

            <li class="list-group-item list-group-item-info" style="width: 150px;">总报名人数</li>
            <li class="list-group-item" style="width: 100px; margin-right: 20px;">{{$total_user_sign}}</li>

            <li class="list-group-item list-group-item-info" style="width: 150px;">总分享人数</li>
            <li class="list-group-item" style="width: 100px; margin-right: 20px;">{{$total_user_share}}</li>

            <li class="list-group-item list-group-item-info" style="width: 150px;">总付款金额</li>
            <li class="list-group-item" style="width: 100px; margin-right: 20px;">{{$total_user_pay}}</li>
        </ul>
    </div>
</div>

<div class="row" style="margin-top: 20px;">
    <div class="col-md-12"><h3>成交线索统计</h3></div>
    <div class="col-md-3">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3 style="color: white;">{{$total_user_today_new}}</h3>
                <p>今日新增</p>
            </div>
            <div class="icon">
                <i class="fa fa-info-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3 style="color: white;">{{$total_user_num}}</h3>
                <p>总人数</p>
            </div>
            <div class="icon">
                <i class="fa fa-info-circle"></i>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12"><h3>总活动数据</h3></div>
    <div class="col-md-4">
        <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-primary">
                昨日
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                访问量
                <span class="badge badge-primary badge-pill">{{$yesterday_view}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                分享量
                <span class="badge badge-primary badge-pill">{{$yesterday_share}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                报名量
                <span class="badge badge-primary badge-pill">{{$yesterday_sign}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                销售金额
                <span class="badge badge-primary badge-pill">{{$yesterday_sale}}</span>
            </li>
        </ul>
    </div>
    <div class="col-md-4">
        <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-primary">
                今日
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                访问量
                <span class="badge badge-primary badge-pill">{{$today_view}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                分享量
                <span class="badge badge-primary badge-pill">{{$today_share}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                报名量
                <span class="badge badge-primary badge-pill">{{$today_sign}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                销售金额
                <span class="badge badge-primary badge-pill">{{$today_sale}}</span>
            </li>
        </ul>
    </div>
    <div class="col-md-4">
        <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-primary">
                总数据
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                访问量
                <span class="badge badge-primary badge-pill">{{$total_view}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                分享量
                <span class="badge badge-primary badge-pill">{{$total_share}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                报名量
                <span class="badge badge-primary badge-pill">{{$total_sign}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                销售金额
                <span class="badge badge-primary badge-pill">{{$total_sale}}</span>
            </li>
        </ul>
    </div>
</div>






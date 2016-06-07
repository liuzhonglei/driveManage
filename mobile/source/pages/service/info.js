/**
 * Created by jf on 15/12/10.
 */

"use strict";

import FontAwesome from 'react-fontawesome';
import WeUI from 'react-weui';
const {Button, Cells, Cell, CellHeader,CellsTitle, CellBody, CellFooter,Toast,Dialog}  = WeUI;
import Page from '../../component/page';
const {Alert} = Dialog;

import './index.less';
import Controller from '../../component/controller'

/**
 * 救援登记界面
 */
export default class ServiceInfo extends Controller {

    /**
     * 加载前
     */
    componentWillMount() {
        super.componentWillMount();
        this.state.info = {
            item: null,
            prefix: null,
            number: null,
            location: "上海",
            time: "",
            apprise_num: 0,
            apprise_level: 0,
            economy_range: {},
            corporation: {}
        }

        // 显示推荐配置
        this.showLoading();

        $.ajax({
            url: './index.php?s=/addon/School/school/getSchoolInfo',
            type: 'POST',
            data: {
                token: this.props.routeParams.token,
                isAjax: true
            },
            success: function (response) {
                //转换条件
                if (response.condition) {
                    var conditions = response.condition.split(',');
                    response.condition = "";
                    var conditionMap = {
                        1: "可夜间练车",
                        2: "可接送",
                        3: "可刷卡",
                        4: "是否速成",
                        5: "可考自动档",
                        6: "可分期"
                    };
                    response.condition = "";
                    for (var i in conditions) {
                        response.condition += " " + conditionMap[conditions[i]];
                    }
                }

                // 转换地点
                if (response.recruit_place) {
                    var placeMap = {
                        1: "思明区",
                        2: "湖里区",
                        3: "集美区",
                        4: "同安区",
                        5: "海沧区",
                        6: "翔安区"
                    };
                    var recruit_place_list = response.recruit_place.split(',');
                    response.recruit_place = "";
                    for (var i in recruit_place_list) {
                        response.recruit_place += " " + placeMap[recruit_place_list[i]];
                    }
                }

                // 设置
                this.setState({info: response});
                this.stopLoading();
            }.bind(this)
        });
    }


    /**
     * 加载
     */
    componentDidMount() {
        //$("[name='rate']").raty({starType: 'i', number: 5, score: this.state.info.apprise_level, readOnly: true});
    }


    /**
     * 显示当前位置
     */
    showPosition() {
        wx.openLocation({
            longitude: 116.306687, // 经度，浮点数，范围为180 ~ -180。
            latitude: 39.984231, // 纬度，浮点数，范围为90 ~ -90
            name: '厦门特运集团有限公司湖滨汽车修理厂', // 位置名
            address: '厦门市湖滨南路8号之二', // 地址详情说明
            scale: 20, // 地图缩放级别,整形值,范围从1~28。默认为最大
            infoUrl: '' // 在查看位置界面底部显示的超链接,可点击跳转
        });
    }

    /**
     * 渲染
     * @returns {XML}
     */
    render() {
        return (
            <div className="page">
                <Toast icon="loading" show={this.state.showLoading}>正在加载中...</Toast>
                <Alert title={this.state.alert.title} buttons={this.state.alert.buttons} show={this.state.showAlert}>
                    {this.state.alert.message}
                </Alert>

                <div className="images_wrapper">
                    <img src={this.state.info.photo}
                         style={{display:"block",border:0, width: "100%",height: "auto",maxidth: "100%"}}
                    />

                    <div className="images_content">&nbsp;&nbsp;{this.state.info.name}</div>
                </div>
                <Cells style={{marginTop:0}}>
                    <Cell>
                        <CellBody>
                            <div onClick={this.showPosition.bind(this)}
                                 style={{float:"left", display: "block",width:"50%",textAlign:"left"}}>
                                <FontAwesome style={{color:"#4397f1"}} name='location-arrow'
                                             size="lg"/>
                                &nbsp;
                                <span style={{color:"#4397f1"}}>到这去</span>
                            </div>
                            <a href={this.state.info.phone? 'tel:'+this.state.info.phone:"javascript:"}

                               style={{float:"right",display: "block",width:"50%",textAlign:"left"}}>
                                <FontAwesome style={{color:"orange"}} name='phone'
                                             size="lg"/>
                                &nbsp;
                                <span style={{color:"#4397f1"}}>联系电话</span>
                            </a>
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader > <FontAwesome style={{color:"orange"}} name='map-marker'
                                                   size="lg"/></CellHeader>
                        <CellBody >
                            &nbsp;
                            {this.state.info.address}
                        </CellBody>
                    </Cell>

                    <Cell>
                        <CellHeader className="half_header">招生地点</CellHeader>
                        <CellBody>
                            <span>{this.state.info.recruit_place}</span>
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="half_header">基本条件</CellHeader>
                        <CellBody>
                            <span>{this.state.info.condition}</span>
                        </CellBody>
                    </Cell>

                </Cells>


                <CellsTitle>提供服务</CellsTitle>
                <Cells access>
                    <Cell href={"#/user/register/"+this.state.info.token}>
                        <CellHeader className="icon_nav">
                            <FontAwesome style={{color:"black"}} name='briefcase'
                                         size="lg"/></CellHeader>

                        <CellBody>
                            <span>报名</span>
                        </CellBody>
                        <CellFooter>
                        </CellFooter>
                    </Cell>
                </Cells>
            </div>
        );
    }
};

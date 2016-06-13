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
 * 服务公司列表界面
 */
export default class ServiceList extends Controller {
    /**
     * 修改选中状态
     * @param item 选中项目
     */
    changeItem(item) {
        if (this.state.item == item) {
            this.state.item = null;
        } else {
            this.state.item = item;
        }

        this.setState({
            item: this.state.item
        })
    }

    /**
     * 取消救援
     */
    cancel() {
        this.setState({
            showConfirm: true,
            confirm: {
                title: '是否取消当前救援?',
                buttons: [{
                    type: 'default',
                    label: '取消',
                    onClick: function () {
                        this.hideConfirm();
                    }.bind(this)
                }, {
                    type: 'primary',
                    label: '确认',
                    onClick: function () {
                        this.hideConfirm();
                    }.bind(this)
                }]
            }
        })
    }

    /**
     * 讲进行加载
     */
    componentWillMount() {
        super.componentWillMount();
        // 初始化
        this.state.info = {};
        this.state.filterOptions = [];
        this.state.param = {
            type: null,
            source: 'companies/list',
            admin_area: '3502'
        }
        this.state.itemEnd = 50;
        this.state.param.order_type = "distance";
        this.state.param.pre_order_type = null;
        this.state.latitude = 0;
        this.state.longitude = 0;

        // 取得当前位置
        console.log('getLocation');
        wx.ready(function () {
            wx.getLocation({
                type: 'gcj02', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                success: function (res) {
                    this.state.latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                    this.state.longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                    this.refreshList();
                }.bind(this)
            });
        }.bind(this))


        // 设置过滤类型
        this.state.filterOptions.push(
            <div>
                <option value="type">安全检测</option>
                <option value="type">技术评定</option>
                <option value="type">环保检测</option>
            </div>
        );

        //  重载数据
        this.reloadData();
    }

    /**
     * 排序对象
     * @param a 对象a
     * @param b 对象b
     */
    sortItems(a, b) {
        //排序
        if (this.state.param.order_type == 'distance') {
            var aDistance = this.computeDistance(a.latitude, a.longitude);
            var bDistance = this.computeDistance(b.latitude, b.longitude);
            var distance = (aDistance ? aDistance : 999999999) - (bDistance ? bDistance : 999999999);
            return distance;
        } else if (this.state.param.order_type == 'level') {
            var result = (a.statics.apprise_level ? a.statics.apprise_level : 0) - (b.statics.apprise_level ? b.statics.apprise_level : 0)
            //console.log('result', result);
            return result;
        }
    }


    /**
     * 搜索当前列表
     */
    search(event) {
        this.state.searchName = event.target.value;
        this.setState({'searchName': this.state.searchName});
        this.refreshList();
    }

    /**
     * 参数修改 刷新界面列表
     * @param event 事件
     * @param param 参数
     */
    paramChange(param, event) {
        this.state.param[param] = event.target.value;
        this.refreshList();
    }

    /**
     * 重载数据
     * @param event 事件
     * @param param 参数
     */
    dataChange(param, event) {
        this.state.param[param] = event.target.value;
        this.refreshList();
    }

    /**
     * 重新请求后台数据
     */
    reloadData() {
        // 加载列表
        this.showLoading();
        $.ajax({
            url: './index.php?s=/addon/School/School/listAllSchool',
            type: 'GET',
            data: {
                "admin_area": this.state.param.admin_area,
                "source": this.state.param.source,
                "type": this.state.param.type
            },
            success: function (response) {
                this.setState({list: response});
                //this.reloadData();
                this.refreshList();
                this.stopLoading();
            }.bind(this),
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.responseJSON) {
                    this.showAlert(jqXHR.responseJSON.message);
                } else {
                    this.showAlert(jqXHR.statusText);

                }
                this.stopLoading();
            }
        })
    }

    /**
     * 更新列表
     */
    refreshList(sort = false) {
        if (!this.state.list) {
            return;
        }

        // 排列数组
        if (this.state.param.pre_order_type != this.state.param.order_type) {
            // TODO
            // this.state.list.sort(this.sortItems.bind(this));

            // 设置上一次排序
            this.state.param.pre_order_type = this.state.param.order_type;
        }


        // 加入
        this.state.items = [];
        this.setState({'items': this.state.items});
        for (var i = 0; i < this.state.list.length && i <= this.state.itemEnd; i++) {
            var item = this.state.list[i];

            //判断地点
            console.log('this.state.param.recruit_place', this.state.param.recruit_place);
            console.log('item.recruit_place', item.recruit_place);

            if (!item.recruit_place || (this.state.param.recruit_place && item.recruit_place.indexOf(this.state.param.recruit_place) < 0)) {
                continue;
            }

            // 判断类型
            if (this.state.param.business_type && item.business_type != this.state.param.business_type) {
                continue;
            }

            var level = item.statics && item.statics.level ? item.statics.level : 5;
            item.levelStar = [];
            for (var j = 0; j < 5; j++) {
                item.levelStar.push(<FontAwesome
                    name='star'
                    size="lg"/>);
            }
            item.credit_result_html = item.credit_result ? <span className="info">信誉等级<span
                className="emphasis"> {item.credit_result}级</span></span> : '';

            //计算距离
            //item.distance = this.computeDistance(item.latitude, item.longitude);
            //item.distance = item.distance ? new Number(this.computeDistance(item.latitude, item.longitude) / 1000).toFixed(1) : null;

            this.state.items.push(
                <Cell
                    style={{display:  !this.state.searchName || item.name.indexOf(this.state.searchName) > -1? '':'none'  }}
                    className="service_list"
                    href={"#/service/info/"+item.token}>
                    <CellHeader>
                    </CellHeader>
                    <CellBody>
                        <h4 className="weui_media_title">
                            {item.name}
                        </h4>

                        <p className="weui_media_desc">

                  <span className="merchant">
                    <span className="title emphasis">
                        {item.levelStar}
                    </span>
                    月<span
                      className="emphasis">{item.statics && item.statics.order_month ? item.statics.order_month : 0}</span>单 &nbsp;&nbsp;&nbsp;
                      累计<span
                      className="emphasis">{item.statics && item.statics.order_total ? item.statics.order_total : 0}</span>单</span>
                            <span className="weui_cell_ft distance"> {item.distance ? item.distance + '千米' : ''}</span>
                            <br/>
                  <span className="remark">
                    {item.address}
                  </span>
                        </p>
                    </CellBody>

                </Cell>
            );
        }

        // 判断是否已经显示全部
        if (this.state.itemEnd < this.state.list.length) {
            this.state.items.push(
                <Cell onClick={function(){this.state.itemEnd +=50;
                                       this.refreshList()}.bind(this)}>
                    <CellHeader></CellHeader>
                    <CellBody style={{textAlign:"center"}}>
                        加载更多
                    </CellBody>
                </Cell>);
        }


        this.setState({'items': this.state.items});

    }

    /**
     * 加载
     */
    componentDidMount() {
        // 初始化搜索栏
        this.initSearchBar("service_list");
    }

    /**
     * 初始化搜索栏
     */
    initSearchBar(searchBarId) {
        var searchInput = $("#" + searchBarId + " [name='search_input']");
        var searchBar = $("#" + searchBarId + " [name='search_bar']");
        var searchShow = $("#" + searchBarId + " [name='search_show']");
        var searchCancel = $("#" + searchBarId + " [name='search_cancel']");
        var searchClear = $("#" + searchBarId + " [name='search_clear']")


        searchInput.focus(function () {
            searchBar.addClass('weui_search_focusing');
        });
        searchInput.blur(function () {
            searchBar.removeClass('weui_search_focusing');
            if ($(this).val()) {
                $("#" + searchBarId + " [name='search_text']").hide();
            } else {
                $("#" + searchBarId + " [name='search_text']").show();
            }
        });
        searchInput.on("input", function () {
            if ($(this).val()) {
                searchShow.show();
            } else {
                searchShow.hide();
            }
        });

        searchBar.on("click", function () {
            searchInput.trigger("focus");
        });
        searchCancel.on("touchend", function () {
            searchShow.hide();
            searchInput.val('');
            this.state.searchName = null;
            this.search({target: {value: null}});
        }.bind(this));
        searchClear.on("touchend", function () {
            searchShow.hide();
            searchInput.val('');
            this.search({target: {value: null}});
        }.bind(this));
    }

    // 组件卸载
    componentWillUnmount() {
        this.state.syncTimer && clearTimeout(this.state.syncTimer);
        this.state.promptTimer && clearTimeout(this.state.promptTimer);
    }

    // 计算与当前坐标之间的距离
    computeDistance(latitude, longitude) {
        var start = new qq.maps.LatLng(this.state.latitude, this.state.longitude), end = new qq.maps.LatLng(latitude, longitude);
        return qq.maps.geometry.spherical.computeDistanceBetween(start, end);
    }

    /**
     * 渲染
     * @returns {XML}
     */
    render() {
        return (
            <div className="page" name="service-div" id="service_list">
                <Toast icon="loading" show={this.state.showLoading}>正在加载中...</Toast>
                <Alert title={this.state.alert.title} buttons={this.state.alert.buttons} show={this.state.showAlert}>
                    {this.state.alert.message}
                </Alert>

                <div className="weui_tab">
                    <div className="weui_navbar">
                        <div className="weui_navbar_item">
                            <select name="recruit_place" className="weui_select"
                                    onChange={this.dataChange.bind(this,"recruit_place")}>
                                <option value="1">思明区</option>
                                <option value="2">湖里区</option>
                                <option value="3">集美区</option>
                                <option value="4">同安区</option>
                                <option value="5">海沧区</option>
                                <option value="6">翔安区</option>
                            </select>
                            &nbsp;
                            <FontAwesome name='angle-down'
                                         size="lg"/>
                        </div>
                        <div className="weui_navbar_item">
                            <select name="order_type" className="weui_select"
                                    onChange={this.paramChange.bind(this,"order_type")}>
                                <option value="distance">距离优先</option>
                                <option value="level">评分优先</option>
                            </select>
                            &nbsp;
                            <FontAwesome name='angle-down'
                                         size="lg"/>
                        </div>
                        <div className="weui_navbar_item">
                            <a href="#/service/map">
                                地图显示&nbsp;
                                <FontAwesome name='map-o'
                                             size="lg"/>
                            </a>
                        </div>
                    </div>
                    <div className="weui_tab_bd">
                        <Cells access>
                            <div className="weui_search_bar" name="search_bar">
                                <form className="weui_search_outer">
                                    <div className="weui_search_inner">
                                        <i className="weui_icon_search"></i>
                                        <input onChange={this.search.bind(this)} type="search"
                                               className="weui_search_input" name="search_input"
                                               placeholder="搜索"
                                               required/>
                                        <a href="javascript:" className="weui_icon_clear" name="search_clear"></a>
                                    </div>
                                    <label for="search_input" className="weui_search_text" name="search_text">
                                        <i className="weui_icon_search"></i>
                                        <span>搜索</span>
                                    </label>
                                </form>
                                <a href="javascript:" className="weui_search_cancel" name="search_cancel">取消</a>
                            </div>
                            {this.state.items}
                        </Cells>
                    </div>
                </div>
            </div>
        );
    }
};

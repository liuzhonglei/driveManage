/**
 * Created by jf on 15/12/10.
 */

"use strict";

import WeUI from 'react-weui'

const {Cells, Cell, CellHeader,CellsTitle, CellBody, CellFooter,Dialog,Toast}  = WeUI;
import Page from '../../component/page';
import Controller from '../../component/controller'
import './maintain.less';
const {Alert} = Dialog;


export default class MaintainList extends Controller {

    /**
     * 查询数据
     */
    componentWillMount() {
        super.componentWillMount();
        this.state.info = {
            name: null,
            carNumber: null,
            transportNumber: null,
            phone: null,
            authCode: null,
            check_corp:{}
        }
        this.state.list = [];
        this.state.items = [];



        // 加载列表
        var url = '/data/second_maintain'
        if(this.props.routeParams.id){
          url+='/'+this.props.routeParams.id
        }
        this.showLoading();
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if (response.success == true) {

                    this.setState({list: response.result});
                    this.refreshList();
                } else {
                    this.showAlert(response.message);
                }
                this.refreshList();
                this.stopLoading();
            }.bind(this),
            error: function (jqXHR, textStatus, errorThrown) {
                    if(jqXHR.responseJSON){
                        this.showAlert(jqXHR.responseJSON.message);
                    }else{
                         this.showAlert(jqXHR.statusText);

                    }
                     this.stopLoading();
                     }
        });
    }


    /**
     * 更新策略
     */
    refreshList() {
        // 加入
        this.state.items = [];
        for (var i = 0; i < this.state.list.length; i++) {

            this.state.items.push(
                <Cell href={'#/maintain/info/'+this.state.list[i].id}>
                        <CellBody>
                            <p className="weui_media_desc">
                            <span className="merchant">
                                <span className="title ">完工时间</span>
                                <span className="info" style={{whiteSpace:'nowrap'}}> {this.state.list[i].send_date}</span>
                            </span>
                                <br/>
                            <span className="merchant">
                                <span className="title ">维护企业</span>
                                <span className="info">{this.state.list[i].check_corp.name}</span>
                            </span>
                                <br/>
                            <span className="merchant">
                                <span className="title ">出厂合格证</span>
                                <span className="info">{this.state.list[i].bill_no}</span>
                            </span>
                            </p>
                        </CellBody>
                        <CellFooter>
                            维护详情
                        </CellFooter>
                 </Cell>
            );
        }
        this.setState({items: this.state.items});
    }


    /**
     * 渲染
     * @returns {XML}
     */
    render() {
        return (
            <Page title="历史维护记录">
                <Toast icon="loading" show={this.state.showLoading}>正在加载中...</Toast>
                <Alert title={this.state.alert.title} buttons={this.state.alert.buttons} show={this.state.showAlert}>
                    {this.state.alert.message}
                </Alert>
                <Cells access>
                    {this.state.items}
                </Cells>
            </Page >
        );
    }
};

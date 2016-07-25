/**
 * Created by jf on 15/12/10.
 */

"use strict";

import WeUI from 'react-weui'
import FontAwesome from 'react-fontawesome';

const {ButtonArea, Button, Cells,CellsTitle, Cell, CellHeader, CellBody, CellFooter,Dialog,Toast}  = WeUI;
import Page from '../../component/page';
import './user.less';
import Controller from '../../component/controller'

const {Alert} = Dialog;
export default class UserInfo extends Controller {
    /**
     * 查询数据
     */
    componentWillMount() {
        super.componentWillMount();
        this.state.info = {
            name: null,
            phone: null,
            carNumber: null,
            transportNumber: null
        }

        this.showLoading();
        $.ajax({
            url: '/data/user_info',
            type: 'GET',
            success: function (response) {
                if (response.success == true) {
                    this.state.info = response.result;
                    this.setState({info: this.state.info});
                } else {
                    this.showAlert(response.message);
                }
                this.stopLoading();
            }.bind(this),
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.responseJSON) {
                    this.showAlert(jqXHR.responseJSON.message);
                } else {
                    this.showAlert(jqXHR.statusText);
                }
                this.stopLoading();

            }.bind(this)
        });
    }

    /**
     * 提交解绑定
     */
    submit() {
        this.showLoading();

        $.ajax({
            url: '/data/user_info',
            type: 'DELETE',
            success: function (response) {
                if (response.success == true) {
                    location.href = "/";
                } else {
                    this.showAlert(response.message);
                }
                this.stopLoading();
            }.bind(this),
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.responseJSON) {
                    this.showAlert(jqXHR.responseJSON.message);
                } else {
                    this.showAlert(jqXHR.statusText);
                }
                this.stopLoading();
            }.bind(this)
        });
    }

    /* 渲染
     * @returns {XML}
     */
    render() {
        return (
            <Page className="cell" title="我的信息">
                <Toast icon="loading" show={this.state.showLoading}>正在加载中...</Toast>
                <Alert title={this.state.alert.title} buttons={this.state.alert.buttons} show={this.state.showAlert}>
                    {this.state.alert.message}
                </Alert>
                <CellsTitle>个人信息</CellsTitle>
                <Cells access>
                    <Cell>
                        <CellHeader className="header">姓名</CellHeader>
                        <CellBody>
                            {this.state.info.name ? this.state.info.name : this.state.info.company_name}
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="header">手机号</CellHeader>
                        <CellBody>
                            {this.state.info.phone}
                        </CellBody>
                    </Cell>
                </Cells>

                <CellsTitle>预约信息</CellsTitle>
                <Cells access>
                    <Cell>
                        <CellHeader className="header">预约驾校</CellHeader>
                        <CellBody>
                            {this.state.info.plate}
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="header">预约时间</CellHeader>
                        <CellBody>
                            {this.state.info.plate_color_name}
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="header">状态</CellHeader>
                        <CellBody>
                            {this.state.info.traffic_card}
                        </CellBody>
                    </Cell>
                </Cells>
                <ButtonArea>
                    <Button type="warn" onClick={this.submit.bind(this)}>解除绑定</Button>
                </ButtonArea>
            </Page>
        );
    }
};

/**
 * Created by jf on 15/12/10.
 */

"use strict";

import WeUI from 'react-weui'
import FontAwesome from 'react-fontawesome';
import Modal from 'react-modal';
const {Dialog,ButtonArea, Button, Cells, Cell, CellHeader, CellBody, CellFooter, CellsTitle,Toast,Icon}  = WeUI;
import Page from '../../component/page';
import './user.less';
import Controller from '../../component/controller'
const {Alert} = Dialog;
import { Grid, Row, Cell as GridCell } from 'react-inline-grid'

var WidthProvider = require('react-grid-layout').WidthProvider;
var ReactGridLayout = require('react-grid-layout');
ReactGridLayout = WidthProvider(ReactGridLayout);

import {
    Form,
    FormCell,
    Radio,
    Input
} from '../../component/form/index.js';

export default class UserRegister extends Controller {

    /**
     * 查询数据
     */
    componentWillMount() {
        super.componentWillMount();
        this.state.submitSign = false;
        this.state.info = {
            //公共信息
            name: null,
            phone: null,
            school_token: null,
            school_name: null
        }

        //查询驾校信息
        $.ajax({
            url: './index.php?s=/addon/School/school/getSchoolInfo',
            type: 'POST',
            data: {
                query_token: this.props.routeParams.token,
                isAjax: true
            },
            success: function (response) {
                this.state.info.school_token = response.token;
                this.state.info.school_name = response.name;
                this.setState({info: this.state.info});
                this.stopLoading();
            }.bind(this)
        });
    }

    /**
     * 判断是否可提交
     */
    allowSubmit() {
        var result = true;
        if (!this.state.info.name || !this.state.info.phone) {
            result = false;
        }

        this.setState({'submitSign': result})
    }

    /**
     * 输入框修改
     * @param event
     */
    valueChange(event) {
        //修改数据
        super.valueChange(event);

        // 判断是否可提交
        this.allowSubmit();
    }

    /**
     * 提交注册
     */
    submit() {
        this.showLoading();
        $.ajax({
            url: './index.php?s=/addon/Student/Student/registerStudent',
            type: 'POST',
            data: this.state.info,
            success: function (response) {
                this.stopLoading();

                if (response.result == '1') {
                    window.location.href = "#/service/info/" + this.state.info.school_token;
                } else {
                    this.showAlert(response.message);
                }
            }.bind(this),
            error: function (jqXHR, textStatus, errorThrown) {
                this.stopLoading();

                if (jqXHR.responseJSON) {
                    this.showAlert(jqXHR.responseJSON.message);
                } else {
                    this.showAlert(jqXHR.statusText);

                }
            }.bind(this)
        });
    }


    /**
     * 渲染
     * @returns {XML}
     */
    render() {
        return (
            <Page className="cell warn" title="报名登记">
                <Toast icon="loading" show={this.state.showLoading}>正在加载中...</Toast>
                <Alert title={this.state.alert.title} buttons={this.state.alert.buttons}
                       show={this.state.showAlert}>
                    {this.state.alert.message}
                </Alert>
                <CellsTitle>信息</CellsTitle>
                <Form>
                    <FormCell>
                        <CellHeader className="half_header">驾校</CellHeader>
                        <CellBody>
                            {this.state.info.school_name}
                        </CellBody>
                        <CellFooter>
                        </CellFooter>
                    </FormCell>
                    <FormCell>
                        <CellHeader className="half_header">姓名</CellHeader>
                        <CellBody>
                            <Input name="name" onChange={this.valueChange.bind(this)}
                                   className="weui_input" type="number" placeholder="请输入姓名"/>
                        </CellBody>
                        <CellFooter>
                        </CellFooter>
                    </FormCell>
                    <FormCell>
                        <CellHeader className="half_header">电话</CellHeader>
                        <CellBody>
                            <Input name="phone" onChange={this.valueChange.bind(this)}
                                   className="weui_input"
                                   type="tel" maxLength='11' placeholder="请输入手机号"/>
                        </CellBody>
                        <CellFooter>
                        </CellFooter>
                    </FormCell>
                </Form>
                <ButtonArea >
                    <Button
                        disabled={this.state.submitSign? '': 'disabled'}
                        onClick={this.state.submitSign? this.submit.bind(this) :''}>提交</Button>
                </ButtonArea>
            </Page>

        );
    }
};


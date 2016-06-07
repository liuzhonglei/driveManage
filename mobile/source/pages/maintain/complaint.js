/**
 * Created by jf on 15/12/10.
 */

"use strict";

import WeUI from 'react-weui'

const {ButtonArea, Button,Cells, Cell, CellHeader,CellsTitle, CellBody, CellFooter,Toast}  = WeUI;
import Page from '../../component/page';
import Controller from '../../component/controller'

export default class MaintainComplaint extends Controller {

    /**
     * 查询数据
     */
    componentWillMount() {
        super.componentWillMount();
        this.state.info = {
            content: ''

        }
    }

    /**
     * 提交注册
     */
    submit() {
        this.showLoading();
        $.ajax({
            url: '/data/common',
            type: 'POST',
            data: {
                source: 'maintain/second_maintenance/' + this.props.routeParams.id + '/complaint',
                content: this.state.info.content,
                method: "POST"
            },
            success: function (response) {
                this.stopLoading();
                if (response.success == true) {
                    this.state.showToast = true;
                    this.setState(this.state);
                    this.state.toastTimer = setTimeout(()=> {
                            this.setState({showToast: false});
                            history.go(-1);
                        }
                        , 2000
                    )
                } else {
                    this.showAlert(response.message);
                }

            }.bind(this),
            error: this.error_handle.bind(this)
        });
    }

    /**
     * 渲染
     * @returns {XML}
     */
    render() {
        return (
            <Page title="投诉与建议?">
                <Toast show={this.state.showToast}>投诉成功,请等待联系！</Toast>

                <div className="weui_cells weui_cells_form">
                    <div className="weui_cell">
                        <div className="weui_cell_bd weui_cell_primary">
                            <textarea maxLength='200' name='content' onChange={this.valueChange.bind(this)}
                                      className="weui_textarea"
                                      placeholder="投诉与建议" rows="5"></textarea>

                            <div className="weui_textarea_counter"><span>{this.state.info.content.length}</span>/200
                            </div>
                        </div>
                    </div>
                </div>


                <ButtonArea>
                    <Button onClick={this.submit.bind(this)}>提交</Button>
                </ButtonArea>
            </Page>
        );
    }

};
//
//<div className="weui_cell">
//    <div className="weui_cell_bd weui_cell_primary">
//        <div className="weui_uploader">
//            <div className="weui_uploader_hd weui_cell">
//                <div className="weui_cell_bd weui_cell_primary">照片上传</div>
//                <div className="weui_cell_ft">0/5</div>
//            </div>
//            <div className="weui_uploader_bd">
//                <ul className="weui_uploader_files">
//                    <li className="weui_uploader_file" style={{backgroundImage:"url(http://shp.qpic.cn/weixinsrc_pic/pScBR7sbqjOBJomcuvVJ6iacVrbMJaoJZkFUIq4nzQZUIqzTKziam7ibg/)"}}></li>
//                </ul>
//                <div className="weui_uploader_input_wrp">
//                    <input className="weui_uploader_input" type="file" accept="image/jpg,image/jpeg,image/png,image/gif" multiple />
//                </div>
//            </div>
//        </div>
//    </div>
//</div>


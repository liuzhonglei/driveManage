/**
 * Created by jf on 15/12/10.
 */

"use strict";

import WeUI from 'react-weui';

import Page from '../../component/page';
import Controller from '../../component/controller'

export default class MaintainPhoto extends Controller {


    /**
     * 查询数据
     */
    componentWillMount() {
        super.componentWillMount();
        // 取得照片
        this.state.photos = [
            {
                src: 'https://farm8.staticflickr.com/7263/8168484671_f47b9e53bf_c.jpg',
                width: 681,
                height: 1024,
                aspectRatio: 1.5,
                lightboxImage: {
                    src: 'https://farm9.staticflickr.com/8337/8168490235_0b0dcb1a7d.jpg'
                }
            },
            {
                src: 'https://farm8.staticflickr.com/7263/8168484671_f47b9e53bf_c.jpg',
                width: 681,
                height: 1024,
                aspectRatio: 1.5,
                lightboxImage: {
                    src: 'https://farm9.staticflickr.com/8337/8168490235_0b0dcb1a7d.jpg'
                }
            }
        ];
    }


    /**
     * 渲染
     * @returns {XML}
     */
    render()
    {
        return (
            <div className="page gallery">
                <div className="title">
                    <h1>维护例检记录</h1>
                    <h2>跟踪维护中的所有步骤</h2>
                </div>
                <div className="gallery_div">
                </div>
            </div>
        );
    }
}



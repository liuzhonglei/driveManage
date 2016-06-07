/**
 * Created by jf on 15/12/10.
 */

"use strict";

var React = require('react');
import './page.less';

export default class Page extends React.Component {
    render() {
        const {title, subTitle, spacing, className, children} = this.props;
        return (
            <section className={`page ${className}`}>
                <div className="hd">
                    <h1 className="title">{title}</h1>
                </div>
                <div className={`bd ${spacing ? 'spacing' : ''}`}>
                    {children}
                </div>
            </section>
        );
    }
};

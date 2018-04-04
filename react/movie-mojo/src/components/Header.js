import React, {Component} from 'react';

class Header extends Component {
    render() {
        return (
            <div className="App-header">
                <h2>{this.props.text}</h2>
                <img src="./posters/logo-advocate.png" className="App-logo"/>                
            </div>
        );
    }
}

export default Header;
// components/componentA.js
import React, { Component } from 'react'
import ComponentB from './componentB'
import ComponentC from './componentC'
export default class ComponentA extends Component {
  render() {
    return(
      <div>
        <ComponentB />
        <ComponentC />
      </div>
    )
  }
}
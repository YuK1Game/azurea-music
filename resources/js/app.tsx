import React from 'react'
import ReactDOM from 'react-dom'
 
const App = () => {
    const title : string = 'TypeScript React !!'
    return (
        <h1>{title}</h1>
    )
}
 
ReactDOM.render(
    <App />,
    document.getElementById('app')
)
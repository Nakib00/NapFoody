import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './index.css'
import Login from './Component/Login/Login.jsx'

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <Login></Login>
  </StrictMode>,
)

// src/App.js
import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter as Router, Route, Routes, Navigate } from 'react-router-dom';
import LoginPage from './pages/LoginPage';
import Dashboard from './pages/Dashboard';
import './App.css';
import { CsvDataProvider } from './contexts/CsvDataContext';

const App = () => {
  return (
    <CsvDataProvider>
      <Router>
        <Routes>
          <Route path="/" element={<LoginPage />} />
          <Route path="/dashboard/:userType" element={<Dashboard />} />
          <Route path="*" element={<Navigate to="/" />} />
        </Routes>
      </Router>
    </CsvDataProvider>
  );
};

ReactDOM.render(<App />, document.getElementById('root'));

export default App;

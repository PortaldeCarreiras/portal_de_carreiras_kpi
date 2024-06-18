// pages/Dashboard.js
import React from 'react';
import JobChart from '../components/JobChart';
import TotalEstagiandoChart from '../components/TotalEstagiandoChart';
import AccessChart from '../components/AccessChart';
import { useParams } from 'react-router-dom';

const Dashboard = () => {
  const { userType } = useParams();

  return (
    <div>
      <JobChart />
      <TotalEstagiandoChart /> 
      {userType === 'admin' && <AccessChart />}
    </div>
  );
}

export default Dashboard;

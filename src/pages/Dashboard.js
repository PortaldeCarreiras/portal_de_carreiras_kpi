// src/pages/Dashboard.js
import React from 'react';
import JobChart from '../components/JobChart';
import TotalEstagiandoChart from '../components/TotalEstagiandoChart';
import AccessChart from '../components/AccessChart';
import ConsultaVagasEstagio from '../components/ConsultaVagasEstagio';
import { useParams } from 'react-router-dom';

const Dashboard = () => {
  const { userType } = useParams();

  return (
    <div>
      <JobChart />
      <TotalEstagiandoChart />
      {userType === 'admin' && <AccessChart />}
      <ConsultaVagasEstagio />
    </div>
  );
};

export default Dashboard;

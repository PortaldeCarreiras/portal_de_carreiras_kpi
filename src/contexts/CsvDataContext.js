// src/contexts/CsvDataContext.js
import React, { createContext, useState, useEffect } from 'react';
import axios from 'axios';

export const CsvDataContext = createContext();

export const CsvDataProvider = ({ children }) => {
  const [csvData, setCsvData] = useState([]);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await axios.get('http://localhost:5000/accessData');
        setCsvData(response.data);
      } catch (error) {
        console.error('Erro ao buscar dados:', error);
      }
    };

    fetchData();
  }, []);

  const updateCsvData = async (data) => {
    try {
      await axios.put('http://localhost:5000/accessData', data);
      setCsvData(data);
    } catch (error) {
      console.error('Erro ao atualizar dados:', error);
    }
  };

  return (
    <CsvDataContext.Provider value={{ csvData, updateCsvData }}>
      {children}
    </CsvDataContext.Provider>
  );
};

<template>
  <div class="patient-list">
    <router-link to="/home" class="btn-back">Back to Home</router-link>
    <h2>Patients</h2>
    <div class="patient-list-header">
      <input 
        type="text" 
        v-model="searchQuery" 
        @input="searchPatients" 
        placeholder="Search by name..." 
        class="search-input"
      />
      <router-link to="/patients/create" class="btn-create">Create New Patient</router-link>
    </div>

    <div v-if="loading" class="loading-spinner">
      <div class="spinner"></div>
      <span>Loading patients...</span>
    </div>

    <ul v-if="!loading" class="patient-list-items">
      <li v-for="patient in filteredPatients" :key="patient.id" class="patient-item">
        <div class="patient-details">
          <span 
            class="patient-name" 
            @click="handlePatientClick(patient)"
          >
            {{ patient.first_name }} {{ patient.last_name }}
          </span>
        </div>
        <div v-if="selectedPatient && selectedPatient.id === patient.id && ongoingAdmission" class="admission-info">
          <span><strong>Ward:</strong> {{ ongoingAdmission.ward }}</span>
          <span><strong>Admission Date:</strong> {{ formatDate(ongoingAdmission.datetime_of_admission) }}</span>
          <span v-if="ongoingAdmission.datetime_of_discharge">
            <strong>Discharge Date:</strong> {{ formatDate(ongoingAdmission.datetime_of_discharge) }}
          </span>
        </div>
      </li>
    </ul>
    <div v-if="error" class="error-message">{{ error }}</div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      patients: [],
      searchQuery: '',
      error: null,
      selectedPatient: null,
      ongoingAdmission: null,
      loading: false,
      intervalId: null,
    };
  },
  computed: {
    filteredPatients() {
      const query = this.searchQuery.toLowerCase();
      return this.patients.filter(patient => 
        (`${patient.first_name} ${patient.last_name}`).toLowerCase().includes(query)
      );
    }
  },
  methods: {
    formatDate(dateTime) {
      return new Date(dateTime).toLocaleString();
    },
    fetchPatients() {
      this.loading = true;
      axios.get('http://localhost:8000/api/patients')
        .then(response => {
          this.patients = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Error fetching patients:', error.response ? error.response.data : error.message);
          this.error = 'Failed to load patients. Please try again later.';
          this.loading = false;
        });
    },
    
    handlePatientClick(patient) {
      this.selectedPatient = patient; 
      this.$router.push({ name: 'PatientDetails', params: { id: patient.id } });
    },
    refreshPatients() {
      this.fetchPatients();
    }
  },
  created() {
    this.fetchPatients();
    this.intervalId = setInterval(this.refreshPatients, 30000); // Refresh every 30 seconds
  },
  beforeUnmount() {
    clearInterval(this.intervalId); // Clear the interval when the component is unmounted
  }
};
</script>

<style scoped>
.patient-list {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  margin: 20px;
  padding: 20px;
}

.btn-back {
  padding: 12px 20px;
  background-color: #6c757d;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: bold;
  display: inline-block;
  margin-bottom: 20px;
  transition: background-color 0.3s ease;
}

.btn-back:hover {
  background-color: #5a6268;
}

.admission-info {
  margin-top: 10px;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: #f9f9f9;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.patient-list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.search-input {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 8px;
  margin-right: 15px;
  flex: 1;
  transition: border-color 0.3s ease;
}

.search-input:focus {
  border-color: #007bff;
  outline: none;
}

.btn-create {
  padding: 12px 20px;
  background-color: #007bff;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.btn-create:hover {
  background-color: #0056b3;
}

.patient-list-items {
  list-style-type: none;
  padding: 0;
}

.patient-item {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  margin-bottom: 15px;
  background-color: #fff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s ease, background-color 0.3s ease;
}

.patient-item:hover {
  background-color: #f1f1f1;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.patient-details {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
}

.patient-name {
  font-size: 18px;
  font-weight: bold;
  cursor: pointer;
  flex: 1;
  transition: color 0.3s ease;
}

.patient-name:hover {
  color: #007bff;
}

.error-message {
  color: #dc3545;
  margin-top: 15px;
  font-size: 16px;
}

.loading-spinner {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 20px;
  font-size: 18px;
  color: #007bff;
}

.spinner {
  width: 30px;
  height: 30px;
  border: 4px solid rgba(0, 123, 255, 0.3);
  border-top: 4px solid #007bff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 10px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

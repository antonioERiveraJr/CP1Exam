<template>
  <div class="admission-list">
    <button @click="goBack" class="btn-back">Back to Home</button>
    <h2>Admissions</h2>

    <div class="search-container">
      <input v-model="searchQuery" type="text" placeholder="Search by Patient ID" />
      <button @click="searchAdmissions" class="btn-search">Search</button>
    </div>

    <button @click="toggleDischarged" class="btn-toggle">
      {{ showDischarged ? 'Hide Discharged Admissions' : 'Show Discharged Admissions' }}
    </button>

    <div v-if="loading" class="loading-spinner">
      <div class="spinner"></div>
      <span>Loading admissions...</span>
    </div>

    <ul v-if="!loading && !showDischarged" class="admission-list-items">
      <li v-for="admission in filteredActiveAdmissions" :key="admission.id" class="admission-item" @click="navigateToAdmissionDischarge(admission.id)">
        <div class="admission-details">
          <span><strong>Patient ID:</strong> {{ admission.patient_id }}</span>
          <span><strong>Ward:</strong> {{ admission.ward }}</span>
          <span><strong>Admission Date:</strong> {{ formatDate(admission.datetime_of_admission) }}</span>
        </div>
      </li>
    </ul>

    <ul v-if="!loading && showDischarged" class="admission-list-items">
      <li v-for="admission in filteredDischargedAdmissions" :key="admission.id" class="admission-item" @click="navigateToAdmissionDischarge(admission.id)">
        <div class="admission-details">
          <span><strong>Patient ID:</strong> {{ admission.patient_id }}</span>
          <span><strong>Ward:</strong> {{ admission.ward }}</span>
          <span><strong>Admission Date:</strong> {{ formatDate(admission.datetime_of_admission) }}</span>
          <span><strong>Discharge Date:</strong> {{ formatDate(admission.datetime_of_discharge) }}</span>
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
      admissions: [],
      error: null,
      searchQuery: '',
      showDischarged: false,
      loading: false,
      intervalId: null,
    };
  },
  computed: {
    filteredActiveAdmissions() {
      const query = this.searchQuery.toLowerCase();
      return this.admissions.filter(admission =>
        !admission.datetime_of_discharge && (
          admission.patient_id.toString().includes(query)
        )
      );
    },
    filteredDischargedAdmissions() {
      const query = this.searchQuery.toLowerCase();
      return this.admissions.filter(admission =>
        admission.datetime_of_discharge && (
          admission.patient_id.toString().includes(query)
        )
      );
    }
  },
  methods: {
    formatDate(dateTime) {
      return new Date(dateTime).toLocaleString();
    },
    fetchAdmissions() {
      this.loading = true;
      const params = this.searchQuery ? { patient_id: this.searchQuery } : {};
      const today = new Date().toISOString().split('T')[0];
      axios.get(`http://localhost:8000/api/admissions`, { params: { ...params, date: today } })
        .then(response => {
          this.admissions = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Error fetching admissions:', error.response ? error.response.data : error.message);
          this.error = 'Failed to load admissions. Please try again later.';
          this.loading = false;
          setTimeout(() => {
            this.error = null;
          }, 2000);
        });
    },
    goBack() {
      this.$router.push('/home');
    },
    toggleDischarged() {
      this.showDischarged = !this.showDischarged;
    },
    searchAdmissions() {
      this.fetchAdmissions();
    },
    navigateToAdmissionDischarge(admissionId) {
      this.$router.push({ name: 'AdmissionDischarge', params: { id: admissionId } });
    }
  },
  created() {
    this.fetchAdmissions();
    this.intervalId = setInterval(this.fetchAdmissions, 30000); // Refresh every 30 seconds
  },
  beforeUnmount() {
    clearInterval(this.intervalId); // Clean up the interval on component unmount
  }
};
</script>

<style scoped>
.admission-list {
  font-family: Arial, sans-serif;
  margin: 20px;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 10px;
  background-color: #f9f9f9;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
  position: relative;
}

.btn-back, .btn-search, .btn-toggle {
  margin-bottom: 10px;
  padding: 8px 12px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.btn-back {
  background-color: #007bff;
  color: white;
}

.btn-back:hover {
  background-color: #0056b3;
}

.btn-search, .btn-toggle {
  background-color: #28a745;
  color: white;
}

.btn-search:hover, .btn-toggle:hover {
  background-color: #218838;
}

.search-container {
  margin-bottom: 20px;
  display: flex;
}

.search-container input {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 5px;
  flex: 1;
  margin-right: 10px;
}

.search-container button {
  padding: 8px 12px;
  background-color: #007bff;
  color: white;
}

.search-container button:hover {
  background-color: #0056b3;
}

.admission-list-items {
  list-style-type: none;
  padding: 0;
}

.admission-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  margin-bottom: 10px;
  background-color: #fff;
  transition: background-color 0.3s;
  cursor: pointer;
}

.admission-item:hover {
  background-color: #f1f1f1;
}

.admission-details {
  display: flex;
  flex-direction: column;
  font-size: 16px;
}

.error-message {
  color: #dc3545;
  margin-top: 10px;
}

.loading-spinner {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 20px;
  font-size: 16px;
  color: #007bff;
}

.spinner {
  width: 24px;
  height: 24px;
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

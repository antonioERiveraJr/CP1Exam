<template>
    <div class="login">
      <h2>Login</h2>
      <input v-model="username" type="text" placeholder="Username" class="input-field" />
      <div class="password-wrapper">
        <input 
          v-model="password" 
          :type="showPassword ? 'text' : 'password'" 
          placeholder="Password" 
          class="input-field password-input"
        />
        <div class="show-password">
          <input 
            type="checkbox" 
            v-model="showPassword" 
            id="show-password"
          />
          <label for="show-password">Show</label>
        </div>
      </div>
      <button @click="login" class="login-button">Login</button>
    </div>
  </template>
  
  <script>
  import axios from '../axios';
  
  export default {
    name: 'UserLogin',
    data() {
      return {
        username: '',
        password: '',
        showPassword: false
      };
    },
    methods: {
      async login() {
        try {
          const response = await axios.post('/login', {
            username: this.username,
            password: this.password
          });
          if (response.data.success) {
            localStorage.setItem('user', JSON.stringify(response.data.user));
            this.$router.push('/home');
          } else {
            alert('Login failed. Please try again.');
          }
        } catch (error) {
          console.error('Login error:', error);
          alert('Login failed. Please try again.');
        }
      }
    }
  };
  </script>
  
  <style scoped>
  .login {
    max-width: 400px;
    margin: 50px auto;
    padding: 40px;
    text-align: center;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    font-family: 'Arial', sans-serif;
  }
  
  h2 {
    margin-bottom: 20px;
    color: #333;
    font-size: 24px;
  }
  
  .input-field {
    width: calc(100% - 40px);
    padding: 12px 20px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    transition: border 0.3s ease;
  }
  
  .input-field:focus {
    border-color: #42b983;
    outline: none;
  }
  
  .password-wrapper {
    display: flex;
    align-items: center;
    position: relative;
  }
  
  .password-input {
    flex: 1;
  }
  
  .show-password {
    display: flex;
    align-items: center;
    margin-left: 10px;
  }
  
  .show-password input[type="checkbox"] {
    margin-right: 5px;
  }
  
  .show-password label {
    cursor: pointer;
    color: #42b983;
    font-size: 14px;
  }
  
  .login-button {
    width: calc(100% - 40px);
    padding: 12px 20px;
    background-color: #42b983;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
  }
  
  .login-button:hover {
    background-color: #369d72;
    transform: translateY(-2px);
  }
  
  .login-button:active {
    background-color: #2a8e5f;
    transform: translateY(0);
  }
  </style>
  
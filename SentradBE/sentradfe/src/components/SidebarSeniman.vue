<template>
	<aside :class="`${is_expanded ? 'is-expanded' : ''}`">
	  <div class="logo">
		<img :src="logoURL" alt="Vue" />
	  </div>

	  <div class="menu-toggle-wrap">
		<button class="menu-toggle" @click="ToggleMenu">
		  <span class="material-icons">keyboard_double_arrow_right</span>
		</button>
	  </div>

	  <h3>Menu</h3>
	  <div class="menu">
		<router-link to="/dashboardSeniman" class="button">
		  <span class="material-icons">dashboard</span>
		  <span class="text">Dashboard</span>
		</router-link>
		<router-link to="/portofolio" class="button">
		  <span class="material-icons">description</span>
		  <span class="text">Portofolio</span>
		</router-link>
		<router-link to="/registrasi-user" class="button">
		  <span class="material-icons">app_registration</span>
		  <span class="text">Registrasi</span>
		</router-link>
		<router-link to="/forum" class="button">
		  <span class="material-icons">forum</span>
		  <span class="text">Forum</span>
		</router-link>
        <router-link to="/proyek" class="button">
		  <span class="material-icons">event</span>
		  <span class="text">Event</span>
		</router-link>
	  </div>

	  <div class="flex"></div>

	  <div class="menu">
		<button class="button" @click="logout">
		  <span class="material-icons">logout</span>
		  <span class="text">Logout</span>
		</button>
	  </div>
	</aside>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import logoURL from '../assets/Sentradlogo.png'

export default {
  name: 'Sidebar',
  data() {
    return {
      is_expanded: localStorage.getItem("is_expanded") === "true",
      logoURL: logoURL
    };
  },
  methods: {
    ToggleMenu() {
      this.is_expanded = !this.is_expanded;
      localStorage.setItem("is_expanded", this.is_expanded);
    },
    logout() {
      // Clear user authentication data from local storage
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user_data');

      // Redirect to the login page
      this.$router.push('/login');
    }
  }
}
</script>
<style lang="scss" scoped>
aside {
  display: flex;
  flex-direction: column;

  background-color: orange;
  color: orange;

  width: calc(2rem + 32px);
  overflow: hidden;
  min-height: 100vh;
  padding: 1rem;

  transition: 0.2s ease-in-out;

  .flex {
    flex: 1 1 0%;
  }

  .logo {
    margin-bottom: 1rem;

    img {
      width: 2rem;
    }
  }

  .menu-toggle-wrap {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 1rem;

    position: relative;
    top: 0;
    transition: 0.2s ease-in-out;

    .menu-toggle {
      transition: 0.2s ease-in-out;
      .material-icons {
        font-size: 2rem;
        color: white;
        transition: 0.2s ease-out;
      }

      &:hover {
        .material-icons {
          color: white;
          transform: translateX(0.5rem);
        }
      }
    }
  }

  h3, .button .text {
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
  }

  h3 {
    color: var(--grey);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
  }

  .menu {
    margin: 0.1rem;

    .button {
      display: flex;
      align-items: center;
      text-decoration: none;

      transition: 0.2s ease-in-out;
      padding: 0.3rem 0.5rem 0.2 rem;
      margin-bottom: 0.5rem; /* Ubah atau tambahkan margin sesuai keinginan */

      .material-icons {
        font-size: 1.5 rem;
        color: var(--light);
        transition: 0.2s ease-in-out;
      }
      .text {
        color: var(--light);
        transition: 0.2s ease-in-out;
      }

      &:hover {
        background-color: orangered;

        .material-icons, .text {
          color: white;
        }
      }

      &.router-link-exact-active {
        background-color: orangered;
        border-right: 5px solid rgb(255, 255, 255);

        .material-icons, .text {
          color: white;
        }
      }
    }
  }

  .footer {
    opacity: 0;
    transition: opacity 0.3s ease-in-out;

    p {
      font-size: 0.875rem;
      color: var(--grey);
    }
  }

  &.is-expanded {
    width: var(--sidebar-width);

    .menu-toggle-wrap {
      top: -3rem;

      .menu-toggle {
        transform: rotate(-180deg);
      }
    }

    h3, .button .text {
      opacity: 1;
    }

    .button {
      .material-icons {
        margin-right: 1rem;
      }
    }

    .footer {
      opacity: 0;
    }
  }

  @media (max-width: 1024px) {
    position: absolute;
    z-index: 99;
  }
}
</style>


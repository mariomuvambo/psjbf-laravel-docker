<template>
  <div>Redirecionando...</div>
</template>

<script>
export default {
  mounted() {
    try {
      const params = new URLSearchParams(window.location.search);
      const token = params.get('token');
      const userParam = params.get('user');

      if (!token || !userParam) {
        this.$router.push('/login');
        return;
      }

      // Decodifica com segurança
      let user;
      try {
        user = JSON.parse(decodeURIComponent(userParam));
      } catch (err) {
        console.error('Erro ao decodificar usuário do Google:', err);
        this.$router.push('/login');
        return;
      }

      // Salva no localStorage
      localStorage.setItem('token', token);
      localStorage.setItem('user', JSON.stringify(user));

      // Redireciona para dashboard
      this.$router.push('/dashboard');

    } catch (err) {
      console.error(err);
      this.$router.push('/login');
    }
  }
};
</script>

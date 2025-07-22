<template>
  <div class="carousel-wrapper py-5 mt-5">
    <div class="w-100 mx-0">
      <div class="carousel slide carousel-dark rounded-4 shadow-lg overflow-hidden" id="carouselExampleDark" data-bs-ride="carousel">
        <!-- Indicadores -->
        <div class="carousel-indicators">
          <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
          <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1" aria-label="Slide 2"></button>
          <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>

        <!-- Slides -->
        <div class="carousel-inner">
          <div class="carousel-item active" data-bs-interval="10000">
            <img src="../assets/images/img-1.jpg" class="d-block w-100 slide-img" alt="Primeiro slide" />
            <div class="carousel-caption enhanced-caption">
              <button type="button" class="btn btn-warning btn-lg fw-bold shadow" data-bs-toggle="modal" data-bs-target="#modalHorariosMissas">
                <i class="bi bi-calendar2-week"></i> Horários das Missas
              </button>
              <p class="mt-2 text-white fw-medium">Participe conosco das celebrações!</p>
            </div>
          </div>

          <div class="carousel-item" data-bs-interval="5000">
            <img src="../assets/images/arjun-mj-UEeiZeKFlks-unsplash.jpg" class="d-block w-100 slide-img" alt="Segundo slide" />
            <div class="carousel-caption enhanced-caption">
              <h5>Aniversariantes</h5>
              <p>Aniversariantes da semana</p>
            </div>
          </div>

          <div class="carousel-item">
            <img src="../assets/images/debby-hudson-qa74Yh6-xlc-unsplash.jpg" class="d-block w-100 slide-img" alt="Terceiro slide" />
            <div class="carousel-caption enhanced-caption">
              <h5>Terceiro Slide</h5>
              <p>Conteúdo representativo do terceiro slide.</p>
            </div>
          </div>
        </div>

        <!-- Controles -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Próximo</span>
        </button>
      </div>
    </div>


     <!-- Modal -->
    <div class="modal fade" id="modalHorariosMissas" tabindex="-1" aria-labelledby="modalHorariosMissasLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow-lg border-0">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="modalHorariosMissasLabel">
              <i class="bi bi-clock-history"></i> Horários das Missas
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body bg-light">
            <p class="text-secondary mb-4"><i class="bi bi-info-circle"></i> Confira abaixo os horários atualizados:</p>
            <div class="table-responsive">
              <table class="table table-hover table-bordered align-middle">
                <thead class="table-success text-center">
                  <tr>
                    <th><i class="bi bi-calendar-week"></i> Dia</th>
                    <th><i class="bi bi-clock"></i> Horário</th>
                    <th><i class="bi bi-geo-alt"></i> Local</th>
                  </tr>
                </thead>
                <tbody class="text-center">
                  <tr>
                    <td>Domingo</td>
                    <td>07:00 | 09:30 | 18:00</td>
                    <td><i class="bi bi-building"></i> Igreja Matriz</td>
                  </tr>
                  <tr>
                    <td>Segunda-feira</td>
                    <td>18:30</td>
                    <td><i class="bi bi-house-door"></i> Capela São José</td>
                  </tr>
                  <tr>
                    <td>Quarta-feira</td>
                    <td>18:30</td>
                    <td><i class="bi bi-house-door"></i> Capela Nossa Senhora de Fátima</td>
                  </tr>
                  <tr>
                    <td>Sábado</td>
                    <td>17:00</td>
                    <td><i class="bi bi-building"></i> Igreja Matriz</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              <i class="bi bi-x-circle"></i> Fechar
            </button>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</template>

<style scoped>
.carousel-wrapper {
  margin-top: 70px;
}

.slide-img {
  object-fit: cover;
  height: 420px;
  width: 100%;
}

.enhanced-caption {
  background: rgba(0, 0, 0, 0.6);
  padding: 1rem 1.5rem;
  border-radius: 10px;
  color: white;
  max-width: 600px;
  margin: 0 auto;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
  filter: invert(1);
}

@media (max-width: 768px) {
  .slide-img {
    height: 300px;
  }

  .enhanced-caption {
    font-size: 0.9rem;
    padding: 0.8rem;
    max-width: 90%;
  }
}
</style>


<script>
export default {
  name: "Carousel",
};
</script>

<style scoped>
.carousel-wrapper {
  margin-top: 70px;
}

.slide-img {
  object-fit: cover;
  height: 280px;
  width: 100%;
}

.enhanced-caption {
  background: rgba(0, 0, 0, 0.6);
  padding: 1rem 1.5rem;
  border-radius: 10px;
  color: white;
  max-width: 600px;
  margin: 0 auto;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
  filter: invert(1);
}



@media (max-width: 768px) {
  .slide-img {
    height: 780px;
  }

  .enhanced-caption {
    font-size: 0.9rem;
    padding: 0.8rem;
    max-width: 0%;
  }
}
</style>

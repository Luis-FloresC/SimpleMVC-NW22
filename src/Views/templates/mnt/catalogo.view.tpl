<section class="container-fluid" id="productos">
    <h4 class="my-4 text-center p-3 mb-2 bg-light text-dark">{{PageTitle}}</h4>
    
    <form class="form-inline align-items-center d-flex justify-content-center mb-4" action="index.php" method="GET">
        <input type="hidden" name="page" value="mnt_catalogo"/>
        <input type="hidden" name="PageIndex" value="1" />

        <input type="search" class="form-control col-8" id="UsuarioBusqueda" name="UsuarioBusqueda" value="{{UsuarioBusqueda}}" placeholder="Ingrese su busqueda">
        <button type="submit" class="btn btn-primary mx-2">Buscar</button>
    </form>


    <div class="container-fluid">
        <div class="row align-items-start">
          <div class="col-md-auto">
            <div class="card">
              <h5 class = "card-title text-center">Precios</h5>
              <div class="card-body">
                <form class="align-items-center" action="index.php" method="GET">
                  <input type="hidden" name="page" value="mnt_catalogo"/>
                  <input type="hidden" name="PageIndex" value="1" />
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="UsuarioBusquedaByPrice" value="0-1000" id="UsuarioBusquedaByPrice">
                  <label class="form-check-label" for="flexRadioDefault1">
                    0 - 1000
                  </label>
                </div>
                <br>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="UsuarioBusquedaByPrice" id="UsuarioBusquedaByPrice" value="1500-2000">
                  <label class="form-check-label" for="flexRadioDefault2">
                    1500 - 2000
                  </label>
                </div>
                <br>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="UsuarioBusquedaByPrice" id="UsuarioBusquedaByPrice" value="1000-3000">
                  <label class="form-check-label" for="flexRadioDefault2">
                    1000 - 3000
                  </label>
                </div>
              
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary mx-2">Buscar</button>
              </div>
            </form>
            </div>
          </div>
          <div class="col">
            <div class="row">
                {{foreach Productos}}
            <div class="card" style="width: 18rem;">
               
                <img src="public/imgs/Productos/invPrdImagen1.png" class="card-img-top" alt="new product">
                <div class="card-body">
                  <h5 class="card-title">Lps. {{invPrdPrecioVenta}}</h5>
                  <p class="card-text">{{invPrdDsc}}</p>
                  <button href="#" class="btn btn-primary mx-2"">ver más</button>
                </div>
               
            </div>
            &nbsp;
            {{endfor Productos}}
        </div>
        
          </div>
          <div class="row">
            <div class="col-md-12 d-flex">
                <ul class="pagination mx-auto"> 
                    <li class="page-item {{PreviousState}}">
                        <a class="page-link" href="index.php?page=mnt_catalogo&PageIndex={{Previous}}" aria-label="Previous">
                          <span aria-hidden="true">&laquo;</span>
                          <span class="sr-only">Previous</span>
                        </a>
                    </li>
    
                      {{foreach PageIndexes}}
                        <li class="page-item {{Estado}}"><a class="page-link" href="index.php?page=mnt_catalogo&PageIndex={{Index}}{{if Busqueda}}&UsuarioBusqueda={{Busqueda}}{{endif Busqueda}}">{{Index}}</a></li>
                      {{endfor PageIndexes}}
    
                    <li class="page-item {{NextState}}">
                        <a class="page-link" href="index.php?page=mnt_catalogo&PageIndex={{Next}}" aria-label="Next">
                          <span aria-hidden="true">&raquo;</span>
                          <span class="sr-only">Next</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        </div>
      </div>

    
      <p>{{total22}} </p>
 
</section>
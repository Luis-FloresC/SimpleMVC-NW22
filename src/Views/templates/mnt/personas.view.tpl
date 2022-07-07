<h1>Trabajar con Personas</h1>
<section>
   <table>
       <thead>
           <tr>
               <th>id</th>
               <th>identidad</th>
               <th>nombre</th>
               <th>edad</th>
           <th><a href="index.php?page=Mnt_Persona&mode=INS">Nuevo</a></th>
           </tr>
       </thead>
       <tbody>
           {{foreach Personas}}
           <tr>
               <td>{{id}}</td>
               <td> <a href="index.php?page=Mnt-Persona&mode=DSP&id={{id}}">{{identidad}}</a></td>
               <td>{{nombre}}</td>
               <td>{{edad}}</td>
               <td>
               <a href="index.php?page=Mnt_Persona&mode=UPD&id={{id}}">Editar</a>
               <a href="index.php?page=Mnt_Persona&mode=DEL&id={{id}}">Eliminar</a>
               </td>
           </tr>
           {{endfor Personas}}
       </tbody>
   </table>
</section>
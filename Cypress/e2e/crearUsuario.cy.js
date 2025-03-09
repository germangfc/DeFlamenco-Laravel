describe('template spec', () => {
  it("login de admin, poder crear usuario, buscarlo y eliminarlo", () => {
    cy.visit("http://localhost");
    cy.viewport(1380, 720);
    
    // Esperar a que el registro esté disponible
    cy.get('#register').should('be.visible').click();
    cy.get('#selectEntidad').should('be.visible').select('Cliente');
    
    cy.get('#name').should('be.visible').type('Raúl');
    cy.get('#email').should('be.visible').type('raul@example.com');
    cy.get('#password').should('be.visible').type('raul1234');
    
    // Asegúrate de que el archivo se adjunte correctamente
    cy.get('#avatar').should('exist').attachFile('foto.jpg');
    cy.get('#crearCliente').should('be.visible').click();

    // Verificar el perfil y luego cerrar sesión
    cy.get('#imagenPerfil').should('be.visible').click();
    cy.contains('Cerrar Sesión').should('be.visible').click();

    // Hacer login
    cy.get('#login').should('be.visible').click();
    cy.get('#email').should('be.visible').type('admin@example.com');
    cy.get('#password').should('be.visible').type('12345678');
    cy.get('#submit').should('be.visible').click();
    
    // Esperar y verificar que el perfil de imagen esté visible
    cy.get('#imagenPerfil').should('be.visible').click();
    cy.contains('Clientes').should('be.visible').click();

    // Buscar cliente Raúl y hacer clic en el botón de búsqueda
    cy.get('#buscadorClientes').should('be.visible').type('Raúl');
    cy.get('#buscadorBotonClientes').should('be.visible').click();

    // Asegurarse de que el botón de eliminar esté visible y hacer clic
    cy.get('#eliminarEmpresa').should('be.visible').click();

    // Buscar de nuevo al cliente y verificar que se haya eliminado
    cy.get('#buscadorClientes').should('be.visible').type('Raúl');
    cy.get('#buscadorBotonClientes').should('be.visible').click();
  });
});

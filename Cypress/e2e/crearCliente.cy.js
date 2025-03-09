describe('template spec', () => {
  it("login de admin, poder crear usuario, buscarlo y eliminarlo", () => {
    cy.visit("http://localhost");
    cy.viewport(1380, 720);
    
    // Esperar a que el registro esté disponible
    cy.get('#register').should('be.visible').click();
    cy.get('#selectEntidad').should('be.visible').select('Cliente');
    
    cy.get('#name').should('be.visible').type('Raúl1342');
    cy.get('#email').should('be.visible').type('rraul11222@example.com');
    cy.get('#password').should('be.visible').type('rraul123411');
    
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

    cy.get('#buscadorClientes').type('Raúl');
    cy.get('#botonClientes').click({ force: true });

    // Asegurarse de que el botón de eliminar esté visible y hacer clic
    cy.get('#eliminarCliente').click();

    // Buscar de nuevo al cliente y verificar que se haya eliminado
    cy.get('#buscadorClientes').type('Raúl');
    cy.get('#botonClientes').click({ force: true });
  });
});

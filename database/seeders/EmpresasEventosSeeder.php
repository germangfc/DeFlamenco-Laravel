<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmpresasEventosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Producciones Artisticas Pichons, S.L.',
            'email' => 'operaflamenca@empresa.com',
            'password' => Hash::make('12345678'),
            'tipo' => 'empresa',
            'remember_token' => null,
            'isDeleted' => false,
        ]);

        $user->assignRole('empresa');

        $empresa = Empresa::create([
            'cif' => 'B75422915',
            'name' => 'Producciones Artisticas Pichons, S.L.',
            'direccion' => 'Calle del Norte 9, Madrid',
            'imagen' => 'https://tablaooperaflamenca.com/wp-content/uploads/2024/10/LOGO-FLAMENCO-1.png',
            'telefono' => '912345678',
            'email' => 'operaflamenca@empresa.com',
            'cuentaBancaria' => 'ES9121000418450200051332',
            'usuario_id' => $user->id,
            'isDeleted' => false,
        ]);

        $user2 = User::create([
            'name' => 'Alsi Beral SL',
            'email' => 'alsiberal@empresa.es',
            'password' => Hash::make('12345678'),
            'tipo' => 'empresa',
            'remember_token' => null,
            'isDeleted' => false,
        ]);

        $user2->assignRole('empresa');

        $empresa2 = Empresa::create([
            'cif' => 'B87333126',
            'name' => 'Alsi Beral SL',
            'direccion' => 'Calle Vizconde De Los Asilos 4BAJO LETRA C. 28027, Madrid (Madrid). España',
            'imagen' => 'https://cdn.jobtoday.com/img/013d15bb-8c94-42bb-8790-7cffb6ec89ed/160x160.jpg',
            'telefono' => '912345678',
            'email' => 'alsiberal@empresa.es',
            'cuentaBancaria' => 'ES9121000418450200051332',
            'usuario_id' => $user2->id,
            'isDeleted' => false,
        ]);

        $user3 = User::create([
            'name' => 'AZUL BOTÁNICO, AIE',
            'email' => 'azulbotanico@empresa.es',
            'password' => Hash::make('12345678'),
            'tipo' => 'empresa',
            'remember_token' => null,
            'isDeleted' => false,
        ]);

        $user3->assignRole('empresa');

        $empresa3 = Empresa::create([
            'cif' => 'V75451237',
            'name' => 'AZUL BOTÁNICO, AIE',
            'direccion' => 'Calle Suero de Quiñones, 4 1º C. 28002, Madrid en España',
            'imagen' => 'https://www.musicazul.com/wp-content/uploads/2024/12/nochesdelbotanico2025-.jpg',
            'telefono' => '912345678',
            'email' => 'azulbotanico@empresa.es',
            'cuentaBancaria' => 'ES9121000418450200051332',
            'usuario_id' => $user3->id,
            'isDeleted' => false,
        ]);

        $user4 = User::create([
            'name' => 'MAINAKE MUSIC AGRUP.',
            'email' => 'mainkake@empresa.es',
            'password' => Hash::make('12345678'),
            'tipo' => 'empresa',
            'remember_token' => null,
            'isDeleted' => false,
        ]);

        $user4->assignRole('empresa');

        $empresa4 = Empresa::create([
            'cif' => 'V93721587',
            'name' => 'MAINAKE MUSIC AGRUP.',
            'direccion' => 'CALLE ALEJANDRO CASONA, 42. 29004, MALAGA (MALAGA). ESPAÑA',
            'imagen' => 'https://images.tomaticket.com/img/no-pic.png',
            'telefono' => '912345678',
            'email' => 'mainkake@empresa.es',
            'cuentaBancaria' => 'ES9121000418450200051332',
            'usuario_id' => $user4->id,
            'isDeleted' => false,
        ]);

        $eventos = [
            [
                'nombre' => 'Tablao Opera Flamenca',
                'stock' => 100,
                'fecha' => '2025-11-22',
                'hora' => '20:00',
                'direccion' => 'Calle del Norte 9',
                'ciudad' => 'Madrid',
                'precio' => 20.00,
                'foto' => 'https://www.atrapalo.com/assets/ou/_next/image/?url=https%3A%2F%2Fcdn.atrapalo.com%2Fcommon%2Fphoto%2Fevent%2F4%2F9%2F1%2F5658%2F1607229%2Ftic_0_0.jpg&w=1280&q=75',
                'descripcion' => 'Tablao Opera Flamenca un marco incomparable en el barrio de San Bernardo, en pleno centro de Madrid y único con su teatro flamenco. El teatro esta equipado con asientos para los espectadores en graderío y para que todos puedan ver bien y disfrutar del espectáculo, ¡sin perderse ni un zapateado! Y para los más fanáticos unas mesas VIP a pie de escenario para vivirlo en primera persona.',
                'empresa_id' => $empresa->id,
            ],
            [
                'nombre' => 'Juanlu Montoya y Marta Santos',
                'stock' => 100,
                'fecha' => '2025-05-03',
                'hora' => '21:30',
                'direccion' => 'Calle Tartesios',
                'ciudad' => 'Fuengirola',
                'precio' => 38.50,
                'foto' => 'https://ecientradasprocdn.azureedge.net/image_uploads/attachments/000/014/629/JuanluMontoyaMarenostrum_400x504.jpg',
                'descripcion' => 'Juanlu Montoya, cantautor, está vinculado a la música desde muy joven. A lo largo de su carrera, ha sabido reinventarse en cada disco, moldeando un estilo propio que destaca por su personalidad y forma de interpretar. ',
                'empresa_id' => $empresa->id,
            ],
            [
                'nombre' => 'Los Yakis - Gira en Córdoba',
                'stock' => 100,
                'fecha' => '2025-05-16',
                'hora' => '21:30',
                'direccion' => 'Teatro Axerquia',
                'ciudad' => 'Córdoba',
                'precio' => 27.50,
                'foto' => 'https://ecientradasprocdn.azureedge.net/image_uploads/attachments/000/015/351/LosYakis_400x504.jpg',
                'descripcion' => 'LOS YAKIS LLEGAN A CÓRDOBA CON SU GIRA EN FAMILIA Tras años conquistando corazones con su inconfundible estilo y energía, Los Yakis regresan con su nueva gira en 2025, denominada  "Gira en Familia". Un espectáculo único que promete llevar a todos sus seguidores a un viaje de emociones y ritmos inigualables.',
                'empresa_id' => $empresa->id,
            ],
            [
                'nombre' => 'Los Yakis - Gira en Sevilla',
                'stock' => 100,
                'fecha' => '2025-05-23',
                'hora' => '21:30',
                'direccion' => 'Isla de la Cartuja',
                'ciudad' => 'Sevilla',
                'precio' => 27.50,
                'foto' => 'https://ecientradasprocdn.azureedge.net/image_uploads/attachments/000/014/186/Sevilla_400x504.jpg',
                'descripcion' => 'LOS YAKIS LLEGAN A SEVILLA CON SU GIRA EN FAMILIA Tras años conquistando corazones con su inconfundible estilo y energía, Los Yakis regresan con su nueva gira en 2025, denominada  "Gira en Familia". Un espectáculo único que promete llevar a todos sus seguidores a un viaje de emociones y ritmos inigualables.',
                'empresa_id' => $empresa->id,
            ],
            [
                'nombre' => 'Andaluza Soul by Mónica Tello',
                'stock' => 100,
                'fecha' => '2025-03-23',
                'hora' => '18:30',
                'direccion' => 'Teatro Las Vegas',
                'ciudad' => 'Madrid',
                'precio' => 15.00,
                'foto' => 'https://www.atrapalo.com/assets/ou/_next/image/?url=https%3A%2F%2Fcdn.atrapalo.com%2Fcommon%2Fphoto%2Fevent%2F4%2F9%2F2%2F0318%2F1631235%2Ftic_0_0.jpg&w=1280&q=75',
                'descripcion' => 'Andaluza Soul by Monica Tello ofrece una visión renovada y enriquecida de la cultura flamenca de la mano de la que fue profesora de la escuela de danza de Victor Ullate y bailarina en la zarzuela del Maestro Moreno Torroba',
                'empresa_id' => $empresa2->id,
            ],
            [
                'nombre' => 'Sara Baras - Cádiz',
                'stock' => 100,
                'fecha' => '2025-06-28',
                'hora' => '22:30',
                'direccion' => 'Cadiz',
                'ciudad' => 'Cadiz',
                'precio' => 49.50,
                'foto' => 'https://ecientradasprocdn.azureedge.net/image_uploads/attachments/000/014/993/sarabaras-cadiz-400x504.jpg',
                'descripcion' => 'Entradas para Sara Baras en el Muelle Reina Victoria en Cádiz.',
                'empresa_id' => $empresa2->id,
            ],
            [
                'nombre' => 'Israel Fernández en Málaga',
                'stock' => 100,
                'fecha' => '2025-05-01',
                'hora' => '20:00',
                'direccion' => 'Calle Córdoba',
                'ciudad' => 'Malaga',
                'precio' => 25.50,
                'foto' => 'https://ecientradasprocdn.azureedge.net/image_uploads/attachments/000/014/466/IsraelFernandez_400x504.jpg',
                'descripcion' => 'Adquiere tus entradas para el concierto de Israel Fernández "Por amor al cante" que se celebrará en el Teatro Del Soho Caixabank.',
                'empresa_id' => $empresa2->id,
            ],
            [
                'nombre' => 'José Mercé en Madrid',
                'stock' => 100,
                'fecha' => '2025-05-29',
                'hora' => '20:30',
                'direccion' => 'Teatro Alcalá',
                'ciudad' => 'Madrid',
                'precio' => 31.32,
                'foto' => 'https://ecientradasprocdn.azureedge.net/image_uploads/attachments/000/015/413/JoseMerce_400x504.jpg',
                'descripcion' => 'JOSÉ MERCÉ CANTA A MANUEL ALEJANDRO José Mercé siempre ha admirado profundamente a uno de sus paisanos más ilustres, Manuel Alejandro, a quien quiere dedicar su espectáculo versionando algunas de sus canciones más emblemáticas y llevándolas a varios palos del flamenco',
                'empresa_id' => $empresa2->id,
            ],
            [
                'nombre' => 'Kiki Morente en Sevilla',
                'stock' => 100,
                'fecha' => '2025-03-30',
                'hora' => '18:00',
                'direccion' => 'Hotel Alfonso XIII',
                'ciudad' => 'Sevilla',
                'precio' => 38.60,
                'foto' => 'https://ecientradasprocdn.azureedge.net/image_uploads/attachments/000/013/613/gnkiki-400x504.jpg',
                'descripcion' => 'Adquiere entradas para el concierto Golden Nights Sevilla - Kiki Morente que tendrá lugar en el Salón Real Hotel Alfonso XIII de Sevilla',
                'empresa_id' => $empresa2->id,
            ],
            [
                'nombre' => 'Maka - Cabaret Festival en Almería',
                'stock' => 100,
                'fecha' => '2025-07-25',
                'hora' => '22:00',
                'direccion' => 'Plaza de Toros',
                'ciudad' => 'Roquetas de Mar',
                'precio' => 41.80,
                'foto' => 'https://d2cyzdatssrhg7.cloudfront.net/export/sites/default/ets/.content/products/img/00-00089Dg.jpg?__locale=es',
                'descripcion' => 'Adquiere tus entradas para Maka - Cabaret Festival que se celebra en el Estadio Municipal Antonio Peroles de Roquetas de Mar (Almería).',
                'empresa_id' => $empresa2->id,
            ],
            [
                'nombre' => 'Homenaje a Pepe Habichuela',
                'stock' => 100,
                'fecha' => '2025-06-18',
                'hora' => '21:30',
                'direccion' => 'Real Jardín Botánico Alfonso XIII',
                'ciudad' => 'Madrid',
                'precio' => 35.00,
                'foto' => 'https://ecientradasprocdn.azureedge.net/image_uploads/attachments/000/015/346/250618_HOMENAJE_PEPE_HABICHUELA__400X504.jpg',
                'descripcion' => 'Leyenda en vida del flamenco, el guitarrista Pepe Habichuela recibe el homenaje, a sus ochenta años, de un elenco de renombrados jóvenes músicos que encabeza su hijo, Josemi Carmona.',
                'empresa_id' => $empresa3->id,
            ],
            [
                'nombre' => 'Van Morrison',
                'stock' => 100,
                'fecha' => '2025-06-04',
                'hora' => '20:30',
                'direccion' => 'Real Jardín Botánico Alfonso XIII',
                'ciudad' => 'Madrid',
                'precio' => 10.00,
                'foto' => 'https://ecientradasprocdn.azureedge.net/image_uploads/attachments/000/015/333/250604_VAN_MORRISON_400X504.jpg',
                'descripcion' => 'Nadie ha sabido moldear el soul, el folk, el ryhthmn and blues y el jazz como él. A sus 79 años, la estatura creativa del cantante y compositor de Belfast es inconmensurable, cimentada en discos y canciones totémicas, que son patrimonio de la humanidad.',
                'empresa_id' => $empresa3->id,
            ],
            [
                'nombre' => 'Zahara',
                'stock' => 100,
                'fecha' => '2025-06-15',
                'hora' => '22:00',
                'direccion' => 'Real Jardín Botánico Alfonso XIII',
                'ciudad' => 'Madrid',
                'precio' => 38.50,
                'foto' => 'https://ecientradasprocdn.azureedge.net/image_uploads/attachments/000/014/596/250615_ZAHARA__400X504.png',
                'descripcion' => 'Zahara llega a Noches del Botánico para la esperada primera presentación de Lento Ternura, su nuevo álbum. En un espectáculo único de pop electrónico, la acompañan Martí Perarnau IV, Manuel Cabezalí y Xavi Molero, creando una experiencia que transita entre lo luminoso y lo oscuro. Una noche irrepetible donde Zahara combina nuevas canciones con los grandes éxitos de su carrera.',
                'empresa_id' => $empresa3->id,
            ],
            [
                'nombre' => 'Paco de Lucía Legacy en Madrid',
                'stock' => 100,
                'fecha' => '2025-07-30',
                'hora' => '21:30',
                'direccion' => 'Real Jardín Botánico Alfonso XIII',
                'ciudad' => 'Madrid',
                'precio' => 44.00,
                'foto' => 'https://ecientradasprocdn.azureedge.net/image_uploads/attachments/000/015/331/250730_PACO_DE_LUCIA_400X504.jpg',
                'descripcion' => 'Resumir la importancia capital de Paco de Lucía en la historia de la música es un empeño tan quijotesco que lo mejor que os podemos recomendar es que, para entenderlo, no os perdáis el espectáculo de algunos de los músicos que lo acompañaron durante sus últimos quince años de vida: el de quienes integran el imponente Paco de Lucía Legacy.',
                'empresa_id' => $empresa3->id,
            ],
            [
                'nombre' => 'Marta Santos en Córdoba',
                'stock' => 100,
                'fecha' => '2025-06-28',
                'hora' => '22:00',
                'direccion' => 'Plaza de Toros Los Califas',
                'ciudad' => 'Córdoba',
                'precio' => 22.50,
                'foto' => 'https://d2cyzdatssrhg7.cloudfront.net/export/sites/default/ets/.content/products/img/00-00089es.jpg?__locale=es',
                'descripcion' => 'Adquiere tus entradas para el concierto de Marta Santos que se celebrará en la Plaza de Toros Los Califas de Córdoba.',
                'empresa_id' => $empresa4->id,
            ],
            [
                'nombre' => 'La Macanita Oro Molío y Rafael De Utrera',
                'stock' => 100,
                'fecha' => '2025-03-16',
                'hora' => '20:00',
                'direccion' => 'Calle Larios',
                'ciudad' => 'Malaga',
                'precio' => 20.00,
                'foto' => 'https://ecientradasprocdn.azureedge.net/image_uploads/attachments/000/014/465/LaMacanita_400x504.jpg',
                'descripcion' => 'En este espectáculo, La Macanita explora y hace un recorrido por los cantes más identitarios que la han visto crecer desde el barrio de Santiago. Santiago y La Plazuela son los barrios más señeros y más flamencos de Jerez. Dos formas únicas y diferentes de cantes y dos formas de expresar un mismo sentimiento. El aire inconfundible de las casas más representativas de Jerez.',
                'empresa_id' => $empresa4->id,
            ],
            [
                'nombre' => 'Mayte Martín en Málaga',
                'stock' => 100,
                'fecha' => '2025-05-25',
                'hora' => '20:00',
                'direccion' => 'Calle Larios',
                'ciudad' => 'Malaga',
                'precio' => 20.00,
                'foto' => 'https://ecientradasprocdn.azureedge.net/image_uploads/attachments/000/014/511/MayteMartin_400x504.jpg',
                'descripcion' => 'La artista rinde homenaje a los grandes maestros del flamenco en este espectáculo Flamenco Íntimo es una incitación al recuerdo. a esa acción sanadora de agitar la memoria para rendir culto a lo que nos precedió y agradecer lo que nos fue concedido. El acto de detenerse a reflexionar sobre lo esencial.',
                'empresa_id' => $empresa4->id,
            ],
            [
                'nombre' => 'Laura Gallego en Málaga',
                'stock' => 100,
                'fecha' => '2025-04-09',
                'hora' => '20:00',
                'direccion' => 'Calle Larios',
                'ciudad' => 'Malaga',
                'precio' => 20.00,
                'foto' => 'https://ecientradasprocdn.azureedge.net/image_uploads/attachments/000/014/513/LauraGallego_400x504.jpg',
                'descripcion' => 'La artista jerezana dedica su cante a las marchas procesionales Un adelanto de la semana Pasión con este espléndido espectáculo de una hora y media de duración en el que la artista realza su lado más cofrade, con referencias eclesiásticas, saetas y sevillanas dirigidas a la Semana Santa y sus imágenes.',
                'empresa_id' => $empresa4->id,
            ],
        ];

        foreach ($eventos as $evento) {
            Evento::create($evento);
        }
    }
}

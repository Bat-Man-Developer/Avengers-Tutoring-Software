const countries = [
    "Australia", "Brazil", "Canada", "France", "Germany", "India", "Japan", "South Africa", "United Kingdom", "United States"
];

const provinces = {
    "Australia": ["New South Wales", "Victoria", "Queensland", "Western Australia"],
    "Brazil": ["São Paulo", "Rio de Janeiro", "Minas Gerais", "Bahia"],
    "Canada": ["Ontario", "Quebec", "British Columbia", "Alberta"],
    "France": ["Île-de-France", "Auvergne-Rhône-Alpes", "Nouvelle-Aquitaine", "Occitanie"],
    "Germany": ["Bavaria", "North Rhine-Westphalia", "Baden-Württemberg", "Hesse"],
    "India": ["Maharashtra", "Uttar Pradesh", "Tamil Nadu", "Karnataka"],
    "Japan": ["Tokyo", "Osaka", "Kanagawa", "Aichi"],
    "South Africa": [
        "Eastern Cape", "Free State", "Gauteng", "KwaZulu-Natal", "Limpopo",
        "Mpumalanga", "North West", "Northern Cape", "Western Cape"
    ],
    "United Kingdom": ["England", "Scotland", "Wales", "Northern Ireland"],
    "United States": ["California", "New York", "Texas", "Florida"]
};

const cities = {
    "Auvergne-Rhône-Alpes": ["Lyon", "Grenoble", "Saint-Étienne"],
    "California": ["Los Angeles", "San Francisco", "San Diego"],
    "Eastern Cape": ["Port Elizabeth", "East London", "Mthatha", "Uitenhage", "Queenstown", "King William's Town", "Grahamstown", "Graaff-Reinet", "Cradock", "Butterworth"],
    "England": ["London", "Manchester", "Birmingham"],
    "Free State": ["Bloemfontein", "Welkom", "Bethlehem", "Kroonstad", "Parys", "Sasolburg", "Odendaalsrus", "Phuthaditjhaba", "Virginia", "Botshabelo"],
    "Gauteng": ["Johannesburg", "Pretoria", "Soweto", "Benoni", "Tembisa", "Boksburg", "Centurion", "Germiston", "Krugersdorp", "Vereeniging", "Springs", "Roodepoort", "Randburg"],
    "Île-de-France": ["Paris", "Versailles", "Saint-Denis"],
    "KwaZulu-Natal": ["Durban", "Pietermaritzburg", "Newcastle", "Richards Bay", "Ladysmith", "Port Shepstone", "Empangeni", "Vryheid", "Estcourt", "Ulundi"],
    "Limpopo": ["Polokwane", "Tzaneen", "Phalaborwa", "Mokopane", "Thohoyandou", "Louis Trichardt", "Musina", "Lebowakgomo", "Giyani", "Thabazimbi"],
    "Maharashtra": ["Mumbai", "Pune", "Nagpur"],
    "Mpumalanga": ["Nelspruit", "Witbank", "Secunda", "Middelburg", "Ermelo", "Standerton", "Barberton", "Piet Retief", "Bethal", "Lydenburg"],
    "New South Wales": ["Sydney", "Newcastle", "Wollongong"],
    "New York": ["New York City", "Buffalo", "Albany"],
    "Northern Cape": ["Kimberley", "Upington", "Kuruman", "Springbok", "De Aar", "Calvinia", "Colesberg", "Port Nolloth", "Prieska", "Douglas"],
    "North Rhine-Westphalia": ["Cologne", "Düsseldorf", "Dortmund"],
    "North West": ["Rustenburg", "Klerksdorp", "Potchefstroom", "Mahikeng", "Brits", "Lichtenburg", "Zeerust", "Wolmaransstad", "Vryburg", "Schweizer-Reneke"],
    "Ontario": ["Toronto", "Ottawa", "Hamilton"],
    "Osaka": ["Osaka City", "Sakai", "Higashiosaka"],
    "Quebec": ["Montreal", "Quebec City", "Laval"],
    "Rio de Janeiro": ["Rio de Janeiro City", "Niterói", "São Gonçalo"],
    "São Paulo": ["São Paulo City", "Campinas", "Guarulhos"],
    "Scotland": ["Edinburgh", "Glasgow", "Aberdeen"],
    "Tokyo": ["Shinjuku", "Shibuya", "Chiyoda"],
    "Uttar Pradesh": ["Lucknow", "Kanpur", "Agra"],
    "Victoria": ["Melbourne", "Geelong", "Ballarat"],
    "Western Cape": ["Cape Town", "Stellenbosch", "George", "Paarl", "Worcester", "Oudtshoorn", "Mossel Bay", "Hermanus", "Knysna", "Swellendam"]
};

const countrySelect = document.getElementById('country');
const provinceSelect = document.getElementById('zone');
const citySelect = document.getElementById('city');

// Populate countries
countries.forEach(country => {
    const option = document.createElement('option');
    option.value = country;
    option.textContent = country;
    countrySelect.appendChild(option);
});

// Event listener for country selection
countrySelect.addEventListener('change', function() {
    provinceSelect.innerHTML = '<option value="">Select Province...</option>';
    citySelect.innerHTML = '<option value="">Select City...</option>';

    if (this.value && provinces[this.value]) {
        provinces[this.value].forEach(province => {
            const option = document.createElement('option');
            option.value = province;
            option.textContent = province;
            provinceSelect.appendChild(option);
        });
    }
});

// Event listener for province selection
provinceSelect.addEventListener('change', function() {
    citySelect.innerHTML = '<option value="">Select City...</option>';
    
    if (this.value && cities[this.value]) {
        cities[this.value].forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
    }
});
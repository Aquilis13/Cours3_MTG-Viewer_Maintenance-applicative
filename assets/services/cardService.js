export async function fetchAllCards() {
    const response = await fetch('/api/card/all');
    if (!response.ok) throw new Error('Failed to fetch cards');
    const result = await response.json();
    return result;
}

export async function fetchCard(uuid) {
    const response = await fetch(`/api/card/${uuid}`);
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch card');
    const card = await response.json();
    card.text = card.text.replaceAll('\\n', '\n');
    return card;
}

/**
 * La recherche se base soit sur la chaine contenu dans le nom ou la correspondance à l'uuid d'une carte
 * 
 * @param {String} search 
 * @returns 
 */
export async function fetchCardBySearch(search) {
    const response = await fetch(`/api/card/search/${search}`);
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch card');
    const result = await response.json();

    return result;
}

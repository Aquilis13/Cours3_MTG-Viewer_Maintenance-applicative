/**
 * Renvoie toutes les cartes
 * 
 * @param {String} setCode 
 * @returns 
 */
export async function fetchAllCards(page, setCode) {
    const response = setCode != null && setCode != ""
        ? await fetch(`/api/card/all?page=${page}&set_code=${setCode}`)
        : await fetch(`/api/card/all?page=${page}`)
    ;
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
export async function fetchCardBySearch(search, setCode) {
    const response = setCode != null && setCode != ""
        ? await fetch(`/api/card/search/${search}?set_code=${setCode}`)
        : await fetch(`/api/card/search/${search}`)
    ;
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch card');
    const result = await response.json();

    return result;
}

/**
 * Récupère l'intégraliter des setCode disponible
 * 
 * @returns 
 */
export async function fetchAllSetCode() {
    const response = await fetch(`/api/card/setcode/all`);
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch setCode');
    const result = await response.json();

    return result;
}

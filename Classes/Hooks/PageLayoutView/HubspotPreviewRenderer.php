<?php
declare (strict_types = 1);

namespace T3G\Hubspot\Hooks\PageLayoutView;

use T3G\Hubspot\Repository\HubspotFormRepository;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Renders the content element preview for hubspot content elements in page module.
 *
 */
class HubspotPreviewRenderer implements PageLayoutViewDrawItemHookInterface
{

    /**
     * Preprocesses the preview rendering of a content element of type "hubspot_form".
     *
     * @param \TYPO3\CMS\Backend\View\PageLayoutView $parentObject Calling parent object
     * @param bool $drawItem Whether to draw the item using the default functionality
     * @param string $headerContent Header content
     * @param string $itemContent Item content
     * @param array $row Record row of tt_content
     *
     * @return void
     */
    public function preProcess(
        PageLayoutView &$parentObject,
        &$drawItem,
        &$headerContent,
        &$itemContent,
        array &$row
    ) {
        if ($row['CType'] === 'hubspot_form') {
            $drawItem = false;
            $this->renderHubspotFormPreview($itemContent, $row);
        }
    }

    /**
     * Transform form structure from hubspot API to field list.
     *
     * @param array $form
     * @return array
     */
    protected function getFormFieldLabels(array $form) : array
    {
        $formFieldGroups = $form['formFieldGroups'] ?? [];
        $fields = [];
        foreach ($formFieldGroups as $formFieldGroup) {
            if (isset($formFieldGroup['fields'])) {
                foreach ($formFieldGroup['fields'] as $field) {
                    $fields[] = $field['label'];
                }
            }
        }
        return $fields;
    }

    /**
     * Renders the hubspot form preview.
     *
     * @param string &$itemContent
     * @param array &$row
     * @return array
     */
    protected function renderHubspotFormPreview(string &$itemContent, array &$row)
    {
        if (!empty($row['hubspot_guid'])) {
            $hubspotFormRepository = GeneralUtility::makeInstance(HubspotFormRepository::class);
            $form = $hubspotFormRepository->getFormForPreview($row['hubspot_guid']);
            $itemContent .= '<p><strong>Hubspot Form:</strong> <br />' . $form['name'] . '</p>';
            $fields = $this->getFormFieldLabels($form);
            $itemContent .= '<p><strong>Fields:</strong> ' . implode(', ', $fields) . '</p>';
        } else {
            $itemContent .= '<div class="callout-warning">No form selected!</div>';
        }
    }
}
